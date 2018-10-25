<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CampaignBundle\Model\EventModel;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\BuilderTokenHelper;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Event as Events;
use Mautic\PageBundle\PageEvents;
use Mautic\LeadBundle\LeadEvent;
use Mautic\PageBundle\Event\PageHitEvent;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenHTMLReplacer;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Recommender\RecommApi\Requests\AddDetailView;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;

/**
 * Class PageSubscriber.
 */
class PageSubscriber extends CommonSubscriber
{
    /**
     * @var RecommenderHelper
     */
    protected $recommenderHelper;

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * @var ApiCommands
     */
    private $apiCommands;

    /**
     * @var RecommenderTokenHTMLReplacer
     */
    private $HTMLReplacer;

    /**
     * @var EventModel
     */
    private $eventModel;


    /**
     * PageSubscriber constructor.
     *
     * @param RecommenderHelper            $recommenderHelper
     * @param RecommenderTokenReplacer     $recommenderTokenReplacer
     * @param ApiCommands               $apiCommands
     * @param RecommenderTokenHTMLReplacer $HTMLReplacer
     * @param EventModel                $eventModel
     */
    public function __construct(
        RecommenderHelper $recommenderHelper,
        RecommenderTokenReplacer $recommenderTokenReplacer,
        ApiCommands $apiCommands,
        RecommenderTokenHTMLReplacer $HTMLReplacer,
        EventModel $eventModel
    ) {
        $this->recommenderHelper        = $recommenderHelper;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->apiCommands           = $apiCommands;
        $this->HTMLReplacer          = $HTMLReplacer;
        $this->eventModel = $eventModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_BUILD   => ['onPageBuild', 0],
            PageEvents::PAGE_ON_HIT     => ['onPageHit', 0],
            PageEvents::PAGE_ON_DISPLAY => ['onPageDisplay', 0],
        ];
    }

    /**
     * Add forms to available page tokens.
     *
     * @param PageBuilderEvent $event
     */
    public function onPageBuild(Events\PageBuilderEvent $event)
    {
        if ($event->tokensRequested($this->recommenderHelper->getRecommenderRegex())) {
            $tokenHelper = new BuilderTokenHelper($this->factory, 'recommender');
            $event->addTokensFromHelper($tokenHelper, $this->recommenderHelper->getRecommenderRegex(), 'name', 'id', true);
        }
    }


    /**
     * Trigger actions for page hits.
     *
     * @param PageHitEvent $event
     */
    public function onPageHit(PageHitEvent $event)
    {
        $hit      = $event->getHit();
        if (!$hit->getRedirect() && !$hit->getEmail()) {
            $response = $this->eventModel->triggerEvent('recommender.focus.insert', ['hit' => $hit]);
        }

        $request = $event->getRequest();
        if (!empty($request->get('Recommender'))) {
            $commands = \GuzzleHttp\json_decode($request->get('Recommender'), true);
            foreach ($commands as $apiRequest => $options) {
                $this->apiCommands->callCommand($apiRequest, $options);
            }
        }

    }

    /**
     * @param PageDisplayEvent $event
     */
    public function onPageDisplay(Events\PageDisplayEvent $event)
    {
        if ($event->getPage()) {
            $event->setContent($this->recommenderTokenReplacer->replaceTokensFromContent($event->getContent()));
        }
    }
}
