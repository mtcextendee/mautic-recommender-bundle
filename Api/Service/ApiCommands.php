<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Service;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\ProgressBarHelper;
use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue;
use MauticPlugin\MauticRecommenderBundle\Entity\Property;
use MauticPlugin\MauticRecommenderBundle\Event\SentEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder;
use OpenCloud\Common\Exceptions\JsonError;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ApiCommands
{
    private $interactionRequiredParams = [
        'AddCartAddition' => ['itemId', 'amount', 'price'],
        'AddPurchase'     => ['itemId', 'amount', 'price', 'profit'],
        'AddDetailView'   => ['itemId'],
        'AddBookmark'     => ['itemId'],
        'AddRating'       => ['itemId', 'rating'],
        'SetViewPortion'  => ['itemId', 'portion'],
    ];

    /**
     * @var array
     */
    private $commandOutput = [];

    /**
     * @var md5
     */
    private $cacheId;

    /**
     * @var RecommenderApi
     */
    private $recommenderApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SegmentMapping
     */
    protected $segmentMapping;

    /**
     * @var RecommenderTokenFinder
     */
    private $recommenderTokenFinder;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ApiCommands constructor.
     *
     * @param RecommenderApi           $recommenderApi
     * @param LoggerInterface          $logger
     * @param TranslatorInterface      $translator
     * @param SegmentMapping           $segmentMapping
     * @param RecommenderTokenFinder   $recommenderTokenFinder
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        RecommenderApi $recommenderApi,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SegmentMapping $segmentMapping,
        RecommenderTokenFinder $recommenderTokenFinder,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $entityManager
    ) {

        $this->recommenderApi         = $recommenderApi;
        $this->logger                 = $logger;
        $this->translator             = $translator;
        $this->segmentMapping         = $segmentMapping;
        $this->recommenderTokenFinder = $recommenderTokenFinder;
        $this->dispatcher             = $dispatcher;
        $this->entityManager          = $entityManager;
    }

    /**
     * @param       $apiRequest
     * @param array $options
     */
    public function callCommand($apiRequest, array $options = [])
    {
        try {
            $return = $this->recommenderApi->getClient()->send($apiRequest, $options);
        } catch (\Exception $e) {
            $return = false;
        }

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_EVENT_SENT)) {
            $event = new SentEvent($apiRequest, $options, $return);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_EVENT_SENT, $event);
            $return = $event->getReturn();
            unset($event);
        }

        return $return;
    }

    /**
     * @param RecommenderToken $recommenderToken
     */
    public function getResults(RecommenderToken $recommenderToken)
    {
        return $this->recommenderApi->getClient()->display($recommenderToken);

    }

    public function ImportUser($lead)
    {
    }

    /**
     * @param     $items
     * @param int $batchSize
     */
    public function ImportItems($items, $batchSize = 50, $timeout = '-1 day', Output $output)
    {
        $clearBatch = 10;
        do {
            $i        = 1;
            $progress = ProgressBarHelper::init($output, $batchSize);
            $progress->start();
            try {
                $counter = 0;
                foreach ($items as $key => $item) {
                    $i += $this->recommenderApi->getClient()->send(
                        'ImportItems',
                        $item,
                        ['timeout' => $timeout]
                    );
                    if ($i == 0) {
                        $item;
                    }
                    $progress->setProgress($i);
                    if ($i % $clearBatch === 0) {
                        $this->entityManager->clear(Item::class);
                        $this->entityManager->clear(Property::class);
                    }
                    if ($i % $batchSize === 0) {
                        $batchSize = 0;
                        $progress->finish();
                        break;
                    }
                }
            } catch (\Exception $error) {
                echo $error->getMessage();
            }
        } while ($batchSize > 0);
    }

    /**
     * @return mixed
     */
    public function getCommandOutput()
    {
        return $this->commandOutput[$this->getCacheId()];
    }

    /**
     * @param mixed $commandOutput
     */
    public function hasCommandOutput()
    {
        if (!empty($this->commandOutput[$this->getCacheId()])) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $commandOutput
     */
    public function setCommandOutput(
        $commandOutput
    ) {
        $this->commandOutput[$this->getCacheId()] = $commandOutput;
    }

    public function getCommandResult()
    {
        $errors  = [];
        $results = $this->getCommandOutput();
        if (is_array($results)) {
            foreach ($results as $result) {
                if (!empty($result['json']['error'])) {
                    $errors[] = $result['json']['error'];
                }
            }
        }
        if (!empty($errors)) {
            throw new \Exception($errors);
        }

        return true;
    }

    /**
     * @return
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * @param  $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = serialize($cacheId);
    }

    /**
     * Display commands results
     *
     * @param array  $results
     * @param string $title
     */
    private function displayCmdTextFromResult(
        array $results,
        $title = '',
        OutputInterface $output
    ) {
        $errors = [];
        foreach ($results as $result) {
            if (!empty($result['json']['error'])) {
                $errors[] = $result['json']['error'];
            }
        }
        // just add empty space
        if ($title != '') {
            $title .= ' ';
        }
        $errors = [];
        $output->writeln(sprintf('<info>Procesed '.$title.count($results).'</info>'));
        $output->writeln('Success '.$title.(count($results) - count($errors)));
        /*if (!empty($errors)) {
            $output->writeln('Errors '.$title.count($errors));
            $output->writeln($errors, true);
        }*/
    }

}

