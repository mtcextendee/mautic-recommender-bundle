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
use MauticPlugin\MauticRecommenderBundle\Entity\Property;
use MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ApiCommands
{
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
     * @param RecommenderTokenFinder   $recommenderTokenFinder
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        RecommenderApi $recommenderApi,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        RecommenderTokenFinder $recommenderTokenFinder,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $entityManager
    ) {
        $this->recommenderApi         = $recommenderApi;
        $this->logger                 = $logger;
        $this->translator             = $translator;
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
        return $this->recommenderApi->getClient()->send($apiRequest, $options);
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
    public function importItems($items, $batchSize = 50, $timeout = RecommenderIntegration::IMPORT_TIMEOUT, Output $output)
    {
        $clearBatch = 10;
        do {
            $i        = 1;
            $progress = ProgressBarHelper::init($output, $batchSize);
            $progress->start();
            try {
                foreach ($items as $key => $item) {
                    $i += $this->recommenderApi->getClient()->send(
                        'ImportItems',
                        $item,
                        ['timeout' => $timeout]
                    );
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

                if ($i < $batchSize) {
                    $batchSize = 0;
                }

            } catch (\Exception $error) {
                $progress->finish();
                $output->writeln('');
                $output->writeln($error->getMessage());

                return;
            }
        } while ($batchSize > 0);

        $output->writeln('');
        $output->writeln('Imported '.$i.' items');
    }

    /**
     * @param     $items
     * @param int $batchSize
     */
    public function deactivateMissingItems($items, Output $output)
    {
        $itemsInJson = [];
        foreach ($items as $key => $item) {
            $itemsInJson[] = $item['itemId'];
        }
        $itemRepository             = $this->entityManager->getRepository('MauticRecommenderBundle:Item');
        $activeItemsMissingFromJson = $itemRepository->findActiveExcluding($itemsInJson);

        $i        = 0;
        $progress = ProgressBarHelper::init($output, count($activeItemsMissingFromJson));
        $progress->start();

        foreach ($activeItemsMissingFromJson as $key => $item) {
            ++$i;

            $itemEntity = $itemRepository->findOneBy(['itemId' => $item['item_id']]);
            $itemEntity->setActive(false);
            $itemRepository->saveEntity($itemEntity);

            $progress->setProgress($i);
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln('Deactivated '.$i.' items that were missing from the json');
    }
}
