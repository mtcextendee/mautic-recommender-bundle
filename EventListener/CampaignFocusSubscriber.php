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

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\PageBundle\Helper\TrackingHelper;
use MauticPlugin\MauticFocusBundle\FocusEvents;
use MauticPlugin\MauticFocusBundle\Model\FocusModel;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEmailSendType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderFocusType;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\HttpFoundation\Session\Session;

class CampaignFocusSubscriber extends CommonSubscriber
{

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * @var FocusModel
     */
    private $focusModel;

    /**
     * @var TrackingHelper
     */
    private $trackingHelper;

    /**
     * @var Session
     */
    private $session;


    /**
     * @param FocusModel               $focusModel
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     * @param TrackingHelper           $trackingHelper
     * @param Session                  $session
     */
    public function __construct(
        FocusModel $focusModel,
        RecommenderTokenReplacer $recommenderTokenReplacer,
        TrackingHelper $trackingHelper,
        Session $session
    ) {
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->focusModel = $focusModel;
        $this->trackingHelper = $trackingHelper;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD             => ['onCampaignBuild', 0],
            RecommenderEvents::ON_CAMPAIGN_TRIGGER_DECISION => ['onCampaignTriggerDecisionInjectRecommenderFocus', 0],
            FocusEvents::TOKEN_REPLACEMENT => ['onTokenReplacement', 200],
        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addDecision(
            'recommender.focus.insert',
            [
                'label'                  => 'mautic.recommender.focus.insert.campaign.event.send',
                'description'            => 'mautic.recommender.focus.insert.campaign.event.send.desc',
                'eventName'              => RecommenderEvents::ON_CAMPAIGN_TRIGGER_DECISION,
                'formType'               => RecommenderFocusType::class,
                'formTypeOptions'        => [
                    'update_select' => 'campaignevent_properties_focus',
                    'urls'          => true,
                ],
                'channel'         => 'focus',
                'channelIdField'  => 'focus',
            ]
        );
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecisionInjectRecommenderFocus(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('recommender.focus.insert')) {
            return;
        }
        $focusId = (int) $event->getConfig()['focus']['focus'];
        if (!$focusId) {
            return $event->setFailed('Focus ID #'.$focusId.' doesn\'t exist.');
        }
        /** @var Focus $focus */
        $focus = $this->focusModel->getEntity($focusId);

        // Stop If Focus not exist or not published
        if (!$focus || !$focus->isPublished()) {
            return $event->setFailed('Focus ID #'.$focusId.' doesn\'t exist or is not  published.');
        }

        $eventDetails = $event->getEventDetails();
        $eventConfig = $event->getConfig();
        // STOP sent campaignEventModel just if Focus Item is opened
        if (!empty($eventDetails['hit'])) {
            $hit = $eventDetails['hit'];
            $includeUrls = (array) $eventConfig['includeUrls']['list'];
            if (!empty($includeUrls)) {
                if (UrlMatcher::hasMatch($includeUrls, $hit->getUrl()) === false) {
                    return $event->setResult(false);
                }
            }
            $excludeUrls = (array) $eventConfig['excludeUrls']['list'];
            if (!empty($excludeUrls)) {
                if (UrlMatcher::hasMatch($excludeUrls, $hit->getUrl())) {
                    return $event->setResult(false);
                }
            }
        }
        $campaignId = $event->getEvent()['campaign']['id'];
        $leadId     = $event->getLead()->getId();
        $config = $event->getConfig();
        $event->setChannel('recommender-focus', $focusId);
        $this->recommenderTokenReplacer->getRecommenderToken()->setConfig($leadId, 'campaign', $campaignId, $config, $this->focusModel->getContent($focus->toArray()));
        // check if cart has some items
        if (!$this->recommenderTokenReplacer->hasItems()) {
            return $event->setFailed(
                'No recommender results for this contact #'.$leadId.' and  focus  #'.$focusId
            );
        }
        $tokens      = $this->recommenderTokenReplacer->getReplacedTokens();
        $contentHash = md5(serialize($tokens));
        $this->session->set($contentHash, serialize($tokens));
        $values['focus_item'][] = [
            'id' => $focusId,
            'js' => $this->router->generate(
                'mautic_focus_generate',
                ['id' => $focusId, 'recommender' => $contentHash],
                true
            ),
        ];
        $this->trackingHelper->updateSession($values);
        return $event->setResult(array_merge($this->getDefaultRecommenderResults($event), ['event'=>$event, 'tokens'=>$tokens]));
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    private function getDefaultRecommenderResults(CampaignExecutionEvent $event)
    {
        return [
            'type'       => $event->getConfig()['type'],
            'campaignId' => $event->getEvent()['campaign']['id'],
        ];
    }

    /**
     * @param TokenReplacementEvent $event
     */
    public function onTokenReplacement(TokenReplacementEvent $event)
    {
        if (!$this->request->get('recommender') || empty($event->getClickthrough()['focus_id'])) {
            return;
        }
        $focus = $this->focusModel->getEntity($event->getClickthrough()['focus_id']);
        if (!$focus) {
            return;
        }
        /** @var Lead $lead */
        $content = $event->getContent();
        if ($content) {
            $tokensFromSession = $this->session->get($this->request->get('recommender'));
            if ($tokensFromSession) {
                $tokens = unserialize($tokensFromSession);
                $content = str_replace(array_keys($tokens), array_values($tokens), $content);
                $event->setContent($content);
            }
        }
    }
}
