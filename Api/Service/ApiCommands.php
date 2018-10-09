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
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;
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

    private function optionsChecker($apiRequest, $options)
    {
        $options                   = array_keys($options);
        $interactionRequiredParams = $this->getInteractionRequiredParam($apiRequest);
        if (!isset($interactionRequiredParams['userId'])) {
            $interactionRequiredParams = array_merge(['userId'], $interactionRequiredParams);
        }
        //required params no contains from input
        if (count(array_intersect($options, $interactionRequiredParams)) != count($interactionRequiredParams)) {
            $this->logger->error(
                $this->translator->trans(
                    'mautic.plugin.recommender.api.wrong.params',
                    ['%params' => implode(', ', $options), '%options' => implode(',', $interactionRequiredParams)]
                )
            );

            return false;
        }

        return true;
    }

    /**
     * @param       $apiRequest
     * @param array $batchOptions
     */
    public function callCommand($apiRequest, array $batchOptions = [])
    {
        // not batch
        if (!isset($batchOptions[0])) {
            $batchOptions = [$batchOptions];
        }
        $command  = 'Recommender\RecommApi\Requests\\'.$apiRequest;
        $requests = [];
        foreach ($batchOptions as $options) {
            $userId = null;
            if (isset($options['userId'])) {
                $userId = $options['userId'];
                unset($options['userId']);
            }
            $itemId = null;
            if (isset($options['itemId'])) {
                $itemId = $options['itemId'];
                unset($options['itemId']);
            }
            $options['cascadeCreate'] = true;
            $req = '';
            switch ($apiRequest) {
                case "ListItemProperties":
                case "ListUserProperties":
                    $req = new $command();
                    break;
                case "AddDetailView":
                case "DeleteCartAddition":
                case "AddBookmark":
                    $req = new $command(
                        $userId,
                        $itemId
                    );
                    break;
                case "AddCartAddition":
                case "AddPurchase":
                $req = new $command(
                    $userId,
                    $itemId,
                    $options
                );
                break;
                case "AddRating":
                    $rating = $options['rating'];
                    unset($options['rating']);
                    $req = new $command(
                        $userId,
                        $itemId,
                        $rating,
                        $options
                    );
                    break;
                case "SetViewPortion":
                    $portion = $options['portion'];
                    unset($options['portion']);
                    $req = new $command(
                        $userId,
                        $itemId,
                        $portion,
                        $options
                    );

                    break;
                case "RecommendItemsToUser":
                    $limit = $options['limit'];
                    unset($options['limit']);
                    $req = new $command(
                        $userId,
                        $limit,
                        $options
                    );
                    break;
            }
            if ($req) {
                $req->setTimeout(5000);
                $requests[] = $req;
            }

            $this->segmentMapping->map($apiRequest, $userId);
        }
        //$this->logger->debug('Recommender requests:'.var_dump($batchOptions));
        $this->setCacheId($requests);
        try {
            //batch processing
            if (count($requests) > 1) {
                $this->setCommandOutput($this->recommenderApi->getClient()->send(new Reqs\Batch($requests)));
            } elseif (count($requests) == 1) {
                $this->setCommandOutput($this->recommenderApi->getClient()->send(end($requests)));
            }
            if ($this->hasCommandOutput()) {
                return $this->getCommandOutput();
            }
        } catch (Ex\ResponseException $e) {
            die($e->getMessage());
            $this->logger->error(
                $this->translator->trans(
                    'mautic.plugin.recommender.api.error',
                    ['%error' => $e->getMessage()]
                )
            );
        }
    }

    public function ImportUser($items)
    {
        $processedData = new ProcessData($items, 'AddUserProperty', 'SetUserValues');
        try {
            $this->callApi($processedData->getRequestsPropertyName());
            $this->callApi($processedData->getRequestsPropertyValues());
        } catch (\Exception $exception) {

        }
    }

    public function ImportItems($items)
    {
        $processedData = new ProcessData($items, 'AddItemProperty', 'SetItemValues');
        try {
            $this->callApi($processedData->getRequestsPropertyName());
            $this->callApi($processedData->getRequestsPropertyValues());
        } catch (\Exception $exception) {

        }
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
        if (empty($requests)) {
            return;
        }

        if (!isset($requests[0])) {
            $requests = [$requests];
        }
        $this->setCacheId($requests);

        if ($this->hasCommandOutput()) {
            return $this->getCommandOutput();
        }
        try {
            //batch processing
            if (count($requests) > 1) {
                $this->setCommandOutput($this->recommenderApi->getClient()->send(new Reqs\Batch($requests)));
            } elseif (count($requests) == 1) {
                $this->setCommandOutput($this->recommenderApi->getClient()->send(end($requests)));
            }
        } catch (Ex\ResponseException $e) {
            throw new \Exception($e->getMessage());
            /* $this->logger->error(
                 $this->translator->trans(
                     'mautic.plugin.recommender.api.error',
                     ['%error' => $e->getMessage()]
                 )
             );*/
        }
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

