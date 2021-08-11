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

use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecommenderGenerator
{
    /**
     * @var \Twig_Extension
     */
    private $twig;
    private $header;
    private $footer;
    /**
     * @var TemplatingHelper
     */
    private $templateHelper;
    /** @var array */
    private $items = [];
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * RecommenderGenerator constructor.
     */
    public function __construct(TemplateModel $recommenderModel, RecommenderApi $recommenderApi, LeadModel $leadModel, \Twig_Environment $twig, ApiCommands $apiCommands, TemplatingHelper $templatingHelper, EventDispatcherInterface $dispatcher)
    {
        $this->twig              = $twig;
        $this->templateHelper    = $templatingHelper;
        $this->dispatcher        = $dispatcher;
    }

    public function getResultByToken(RecommenderToken $recommenderToken)
    {
        if (!$recommenderToken->getRecommender() instanceof Recommender) {
            return;
        }

        $this->items = [];

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS)) {
            $resultEvent = new FilterResultsEvent($recommenderToken);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS, $resultEvent);
            $this->items =  $resultEvent->getItems();
        }

        return $this->items;
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
     * @return string|void
     */
    public function getContentByToken(RecommenderToken $recommenderToken, $view = 'Page')
    {
        if (!$recommenderToken->getRecommender() instanceof Recommender) {
            return;
        }
        $recommenderTemplate = $recommenderToken->getRecommender()->getTemplate();
        $this->items         = $this->getResultByToken($recommenderToken);
        if (empty($this->items)) {
            return;
        }
        if ('basic' == $recommenderTemplate->getTemplateMode()) {
            $headerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/'.$view.':generator-header.html.php',
                [
                    'recommender' => $recommenderTemplate,
                    'settings'    => $recommenderToken->getSettings(),
                ]
            );
            $footerTemplateCore = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/'.$view.':generator-footer.html.php',
                [
                    'recommender' => $recommenderTemplate,
                    'settings'    => $recommenderToken->getSettings(),
                ]
            );
            $bodyTemplateCore   = $this->templateHelper->getTemplating()->render(
                'MauticRecommenderBundle:Builder/'.$view.':generator-body.html.php',
                [
                    'recommender' => $recommenderTemplate,
                    'settings'    => $recommenderToken->getSettings(),
                ]
            );
            $headerTemplate = $this->twig->createTemplate($headerTemplateCore);
            $footerTemplate = $this->twig->createTemplate($footerTemplateCore);
            $bodyTemplate   = $this->twig->createTemplate($bodyTemplateCore);
        } else {
            $headerTemplate = ArrayHelper::getValue('header', $recommenderTemplate->getTemplate());
            if ($headerTemplate) {
                $headerTemplate = $this->twig->createTemplate($headerTemplate);
            }
            $bodyTemplate = $this->twig->createTemplate($recommenderTemplate->getTemplate()['body']);

            $footerTemplate = ArrayHelper::getValue('footer', $recommenderTemplate->getTemplate());
            if ($footerTemplate) {
                $footerTemplate = $this->twig->createTemplate($footerTemplate);
            }
        }

        return $this->getTemplateContent($headerTemplate, $footerTemplate, $bodyTemplate);
    }

    /**
     * @return string
     */
    private function getTemplateContent($headerTemplate, $footerTemplate, $bodyTemplate)
    {
        $output = '';
        if ($headerTemplate) {
            $output .= $headerTemplate->render($this->getFirstItem());
        }
        foreach ($this->getItems() as $i => $item) {
            $item['index'] = $i;
            $output .= $bodyTemplate->render($item);
        }

        if ($footerTemplate) {
            $output .= $footerTemplate->render($this->getFirstItem());
        }

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
            foreach ($item as &$ite) {
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
     * Return new token keys with comma separated item IDs.
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
     * Get first item.
     *
     * @return array
     */
    public function getFirstItem()
    {
        $items = $this->getItems();

        return current($items);
    }
}
