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
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;
use Recommender\RecommApi\Exceptions as Ex;
use Recommender\RecommApi\Requests as Reqs;

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
     * RecommenderGenerator constructor.
     *
     * @param TemplateModel     $recommenderModel
     * @param RecommenderApi    $recommenderApi
     * @param LeadModel         $leadModel
     * @param \Twig_Environment $twig
     * @param ApiCommands       $apiCommands
     * @param TemplatingHelper  $templatingHelper
     */
    public function __construct(
        TemplateModel $recommenderModel,
        RecommenderApi $recommenderApi,
        LeadModel $leadModel,
        \Twig_Environment $twig,
        ApiCommands $apiCommands,
        TemplatingHelper $templatingHelper
    ) {
        $this->recommenderApi    = $recommenderApi;
        $this->recommenderModel  = $recommenderModel;
        $this->leadModel      = $leadModel;
        $this->twig           = $twig;
        $this->apiCommands    = $apiCommands;
        $this->templateHelper = $templatingHelper;
    }

    /**
     * @param RecommenderToken $recommenderToken
     * @param array         $options
     */
    public function getResultByToken(RecommenderToken $recommenderToken, $options = [])
    {
        $hash = md5(\GuzzleHttp\json_encode($recommenderToken).\GuzzleHttp\json_encode($options));
        if (!empty($this->cache[$hash])) {
            return $this->cache[$hash];
        }

        $recommender = $this->recommenderModel->getEntity($recommenderToken->getId());

        if (!$recommender instanceof RecommenderTemplate) {
            return;
        }

        //$options['filter']           = $recommender->getFilter();
        //$options['booster']          = $recommender->getBoost();
        $options['returnProperties'] = true;
        $recommenderToken->setAddOptions($options);
        try {
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
            return $this->items;

        } catch (Ex\ApiTimeoutException $e) {
            die(print_r($e->getMessage()));
            //Handle timeout => use fallback
        } catch (Ex\ResponseException $e) {
            die(print_r($e->getMessage()));
            //Handle errorneous request => use fallback
        } catch (Ex\ApiException $e) {
            die(print_r($e->getMessage()));
            //ApiException is parent of both ResponseException and ApiTimeoutException
        }
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function replaceTagsFromContent($content)
    {
        return $this->twig->createTemplate($content)->render($this->getFirstItem()['values']);
    }

    /**
     * @param RecommenderToken $recommenderToken
     *
     * @return string|void
     */
    public function getContentByToken(RecommenderToken $recommenderToken)
    {
        /** @var RecommenderTemplate $recommender */
        $recommender = $this->recommenderModel->getEntity($recommenderToken->getId());

        if (!$recommender instanceof RecommenderTemplate) {
            return;
        }

        $items = $this->getResultByToken($recommenderToken);
        if (empty($items)) {
            return;
        }
        if ($recommender->getTemplateMode() == 'basic') {
            $headerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:RecommenderTemplate:generator-header.html.php',
                [
                    'recommender' => $recommender,
                ]
            );
            $footerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:RecommenderTemplate:generator-footer.html.php',
                [
                    'recommender' => $recommender,
                ]
            );
            $bodyTemplateCore   = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:RecommenderTemplate:generator-body.html.php',
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
        $output = $headerTemplate->render($this->getFirstItem()['values']);
        foreach ($this->getItems() as $item) {
            $output .= $bodyTemplate->render($item['values']);
        }
        $output.= $footerTemplate->render($this->getFirstItem()['values']);
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
            foreach ($item['values'] as $key => &$ite) {
                if (is_array($ite)) {
                    $ite = implode(', ', $ite);
                }
            }
            $item['values']['keys'] = $keys;
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
        return  implode($separator, array_column($this->items, 'id'));
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

