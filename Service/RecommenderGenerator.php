<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Service;

use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecommenderGenerator
{
    /** @var RecommenderApi */
    private $recommenderApi;

    /**
     * @var TemplateModel
     */
    private $recommenderModel;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var \Twig_Extension
     */
    private $twig;

    /**
     * @var ApiCommands
     */
    private $apiCommands;

    private $header;

    private $footer;

    /**
     * @var TemplatingHelper
     */
    private $templateHelper;

    /** @var array $items */
    private $items = [];

    /** @var array  */
    private $cache = [];

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * RecommenderGenerator constructor.
     *
     * @param TemplateModel            $recommenderModel
     * @param RecommenderApi           $recommenderApi
     * @param LeadModel                $leadModel
     * @param \Twig_Environment        $twig
     * @param ApiCommands              $apiCommands
     * @param TemplatingHelper         $templatingHelper
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        TemplateModel $recommenderModel,
        RecommenderApi $recommenderApi,
        LeadModel $leadModel,
        \Twig_Environment $twig,
        ApiCommands $apiCommands,
        TemplatingHelper $templatingHelper,
        EventDispatcherInterface $dispatcher
    ) {
        $this->recommenderApi    = $recommenderApi;
        $this->recommenderModel  = $recommenderModel;
        $this->leadModel      = $leadModel;
        $this->twig           = $twig;
        $this->apiCommands    = $apiCommands;
        $this->templateHelper = $templatingHelper;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RecommenderToken $recommenderToken
     */
    public function getResultByToken(RecommenderToken $recommenderToken)
    {
        if (!$recommenderToken->getTemplate() instanceof RecommenderTemplate) {
            return;
        }

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS)) {
            $resultEvent = new FilterResultsEvent($recommenderToken);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS, $resultEvent);
        }
        //$this->items =  $this->apiCommands->getResults($recommenderToken);
        $this->items =  $resultEvent->getItems();
        return $this->items;

            /*switch ($recommenderToken->getType()) {
                case "RecommendItemsToUser":
                    $this->apiCommands->callCommand(
                        'RecommendItemsToUser',
                        $recommenderToken->getOptions(['userId', 'limit'])
                    );
                    $items = $this->apiCommands->getCommandOutput();
                    break;
            }
            $this->items = $items['recomms'];
            $this->cache[$hash] = $this->items;
            return $this->items;*///

        //$options['filter']           = $recommender->getFilter();
        //$options['booster']          = $recommender->getBoost();
        /*$options['returnProperties'] = true;
        $recommenderToken->setAddOptions($options);
            switch ($recommenderToken->getType()) {
                case "RecommendItemsToUser":
                    $this->apiCommands->callCommand(
                        'RecommendItemsToUser',
                        $recommenderToken->getOptions(['userId', 'limit'])
                    );
                    $items = $this->apiCommands->getCommandOutput();
                    break;
            }
            $this->items = $items['recomms'];
            $this->cache[$hash] = $this->items;
            return $this->items;*/
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function replaceTagsFromContent($content)
    {
        if ($this->getFirstItem()) {
            return $this->twig->createTemplate($content)->render($this->getFirstItem());
        }
    }

    /**
     * @param RecommenderToken $recommenderToken
     *
     * @return string|void
     */
    public function getContentByToken(RecommenderToken $recommenderToken)
    {
        if (!$recommenderToken->getTemplate() instanceof RecommenderTemplate) {
            return;
        }
        $recommender = $recommenderToken->getTemplate();
        $this->items = $this->getResultByToken($recommenderToken);

        if (empty($this->items)) {
            return;
        }

        if ($recommender->getTemplateMode() == 'basic') {
            $headerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/Page:generator-header.html.php',
                [
                    'recommender' => $recommender,
                ]
            );
            $footerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/Page:generator-footer.html.php',
                [
                    'recommender' => $recommender,
                ]
            );
            $bodyTemplateCore   = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/Page:generator-body.html.php',
                [
                    'recommender' => $recommender,
                ]
            );
            $headerTemplate = $this->twig->createTemplate($headerTemplateCore);
            $footerTemplate = $this->twig->createTemplate($footerTemplateCore);
            $bodyTemplate   = $this->twig->createTemplate($bodyTemplateCore);

        } else {
            $headerTemplate = $this->twig->createTemplate($recommender->getTemplate()['header']);
            $footerTemplate = $this->twig->createTemplate($recommender->getTemplate()['footer']);
            $bodyTemplate   = $this->twig->createTemplate($recommender->getTemplate()['body']);
        }

        return $this->getTemplateContent($headerTemplate, $footerTemplate, $bodyTemplate);
    }

    /**
     *
     * @return string
     */
    private function getTemplateContent($headerTemplate, $footerTemplate, $bodyTemplate)
    {
        $output = $headerTemplate->render($this->getFirstItem());
        foreach ($this->getItems() as $item) {
            $output .= $bodyTemplate->render($item);
        }
        $output.= $footerTemplate->render($this->getFirstItem());
        return $output;
    }


    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $keys = $this->getItemsKeys();
        foreach ($this->items as &$item) {
            foreach ($item as $key => &$ite) {
                if (is_array($ite)) {
                    $ite = implode(', ', $ite);
                }
            }
            $item['keys'] = $keys;
        }
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Return new token keys with comma separated item IDs
     *
     * @param string $separator
     *
     * @return string
     */
    private function getItemsKeys($separator = ',')
    {
        return  implode($separator, array_column($this->items, 'itemId'));
    }

    /**
     * Get first item
     *
     * @return array
     */
    public function getFirstItem()
    {
        $items = $this->getItems();
        return current($items);
    }
}

