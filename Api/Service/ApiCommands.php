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

use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder;
use Psr\Log\LoggerInterface;
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
     * ApiCommands constructor.
     *
     * @param RecommenderApi         $recommenderApi
     * @param LoggerInterface     $logger
     * @param TranslatorInterface $translator
     * @param SegmentMapping      $segmentMapping
     * @param RecommenderTokenFinder $recommenderTokenFinder
     */
    public function __construct(
        RecommenderApi $recommenderApi,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SegmentMapping $segmentMapping,
        RecommenderTokenFinder $recommenderTokenFinder
    ) {

        $this->recommenderApi         = $recommenderApi;
        $this->logger              = $logger;
        $this->translator          = $translator;
        $this->segmentMapping      = $segmentMapping;
        $this->recommenderTokenFinder = $recommenderTokenFinder;
    }

    /**
     * @param       $apiRequest
     * @param array $batchOptions
     */
    public function callCommand($apiRequest, array $batchOptions = [])
    {
        $this->recommenderApi->getClient()->send($apiRequest, $batchOptions);
    }

    public function ImportItems($items)
    {
        $this->recommenderApi->getClient()->send('AddItem', $items);
        $this->recommenderApi->getClient()->send('AddItemProperty', $items);
        $this->recommenderApi->getClient()->send('AddItemPropertyValue', $items);
    }

    /**
     * @param                                                          $content
     * @param int                                                      $minAge
     * @param int                                                      $maxAge
     */
    public function hasAbandonedCart($content, $minAge, $maxAge)
    {
        $tokens = $this->recommenderTokenFinder->findTokens($content);
        if (!empty($tokens)) {
            foreach ($tokens as $key => $token) {
                $this->getAbandonedCart($token, $minAge, $maxAge);
                $items = $this->getcommandoutput();
                if (!empty($items)) {
                    return true;
                }
            }
        }
    }

    public function getAbandonedCart(RecommenderToken $recommenderToken, $cartMinAge, $cartMaxAge)
    {
        $options = [
            "expertSettings" => [
                "algorithmSettings" => [
                    "evaluator" => [
                        "name" => "reql",
                    ],
                    "model"     => [
                        "name"     => "reminder",
                        "settings" => [
                            "parameters" => [
                                "interaction-types"        => [
                                    "detail-view" => [
                                        "enabled" => false
                                    ],
                                    "cart-addition" => [
                                        "enabled" => true,
                                        "weight"  => 1.0,
                                        "min-age" => $cartMinAge,
                                        "max-age" => $cartMaxAge,
                                    ],
                                ],
                                "filter-purchased-max-age" => $cartMaxAge,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $recommenderToken->setAddOptions($options);
        $this->callCommand('RecommendItemsToUser', $recommenderToken->getOptions(true));
        if (!empty($this->getCommandOutput()['recomms'])) {
            return $this->getCommandOutput()['recomms'];
        }

        return [];
    }

    public function callApi($requests)
    {
        $this->setCacheId($requests);

        if (!$this->hasCommandOutput()) {
            $this->setCommandOutput($this->recommenderApi->getClient()->send($requests));
        }
        return $this->getCommandOutput();
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

    /**
     * @return array
     */
    public function getInteractionRequiredParam(
        $key
    ) {
        return $this->interactionRequiredParams[$key];
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

