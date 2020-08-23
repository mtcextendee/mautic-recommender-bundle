<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\DynamicContentBundle\DynamicContentEvents;
use Mautic\DynamicContentBundle\Model\DynamicContentModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\NotificationBundle\NotificationEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticFocusBundle\FocusEvents;
use MauticPlugin\MauticFocusBundle\Model\FocusModel;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

class TokenReplacementSubscriber extends CommonSubscriber
{
    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * @var DynamicContentModel
     */
    private $dynamicContentModel;

    /**
     * @var FocusModel
     */
    private $focusModel;

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    /**
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     * @param DynamicContentModel      $dynamicContentModel
     * @param FocusModel               $focusModel
     * @param IntegrationHelper        $integrationHelper
     * @param ContactTracker           $contactTracker
     */
    public function __construct(
        RecommenderTokenReplacer $recommenderTokenReplacer,
        DynamicContentModel $dynamicContentModel,
        FocusModel $focusModel,
        IntegrationHelper $integrationHelper,
        ContactTracker $contactTracker
    ) {
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->dynamicContentModel      = $dynamicContentModel;
        $this->focusModel               = $focusModel;
        $this->integrationHelper        = $integrationHelper;
        $this->contactTracker = $contactTracker;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicContentEvents::TOKEN_REPLACEMENT       => ['onDynamicContentTokenReplacement', 200],
            FocusEvents::TOKEN_REPLACEMENT                => ['onFocusTokenReplacement', 200],
            NotificationEvents::TOKEN_REPLACEMENT         => ['onNotificationTokenReplacement', 200],
        ];
    }

    /**
     * @param TokenReplacementEvent $event
     */
    public function onDynamicContentTokenReplacement(TokenReplacementEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

        $clickthrough = $event->getClickthrough();
        $leadId       = $clickthrough['lead'];
        $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($leadId);
        $event->setContent($this->recommenderTokenReplacer->replaceTokensFromContent($event->getContent()));
    }

    /**
     * @param TokenReplacementEvent $event
     */
    public function onFocusTokenReplacement(TokenReplacementEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }


        $clickthrough = $event->getClickthrough();

        if (empty($clickthrough['focus_id'])) {
            return;
        }

        if (!empty($clickthrough['lead'])) {
            $leadId       = $clickthrough['lead'];
        } elseif ($contact = $this->contactTracker->getContact()) {
            $leadId = $contact->getId();
        }else{
            return;
        }

        $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($leadId);
        $this->recommenderTokenReplacer->getRecommenderToken()->setContent($event->getContent());
        $event->setContent($this->recommenderTokenReplacer->getReplacedContent());
    }
}
