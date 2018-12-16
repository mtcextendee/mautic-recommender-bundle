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

use Doctrine\ORM\EntityManager;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\DynamicContentBundle\Entity\DynamicContent;
use Mautic\DynamicContentBundle\Model\DynamicContentModel;
use Mautic\EmailBundle\Helper\UrlMatcher;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\EmailBundle\Model\SendEmailToUser;
use Mautic\LeadBundle\Entity\DoNotContact as DNC;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\NotificationBundle\Api\AbstractNotificationApi;
use Mautic\NotificationBundle\Event\NotificationSendEvent;
use Mautic\NotificationBundle\Form\Type\MobileNotificationSendType;
use Mautic\NotificationBundle\Form\Type\NotificationSendType;
use Mautic\NotificationBundle\Model\NotificationModel;
use Mautic\NotificationBundle\NotificationEvents;
use Mautic\PageBundle\Helper\TrackingHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticFocusBundle\Entity\Focus;
use MauticPlugin\MauticFocusBundle\Model\FocusModel;
use MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentRemoveType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEmailSendType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderFocusType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderNotificationSendType;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTagsReplacer;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CampaignSubscriber.
 */
class CampaignSubscriber extends CommonSubscriber
{
    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var EmailModel
     */
    protected $emailModel;


    /**
     * @var SendEmailToUser
     */
    private $sendEmailToUser;

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * @var CampaignLeadDetails
     */
    private $campaignLeadDetails;

    /**
     * @var TrackingHelper
     */
    private $trackingHelper;

    /**
     * @var FocusModel
     */
    private $focusModel;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var DynamicContentModel
     */
    private $dynamicContentModel;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var AbstractNotificationApi
     */
    private $notificationApi;


    /**
     * @param LeadModel               $leadModel
     * @param EmailModel              $emailModel
     * @param EventModel              $eventModel
     * @param SendEmailToUser         $sendEmailToUser
     * @param RecommenderTokenReplacer   $recommenderTokenReplacer
     * @param CampaignLeadDetails     $campaignLeadDetails
     * @param TrackingHelper          $trackingHelper
     * @param FocusModel              $focusModel
     * @param Session                 $session
     * @param IntegrationHelper       $integrationHelper
     * @param DynamicContentModel     $dynamicContentModel
     * @param EntityManager           $entityManager
     *
     * @param NotificationModel       $notificationModel
     *
     * @param AbstractNotificationApi $notificationApi
     *
     * @internal param MessageQueueModel $messageQueueModel
     */
    public function __construct(
        LeadModel $leadModel,
        EmailModel $emailModel,
        EventModel $eventModel,
        SendEmailToUser $sendEmailToUser,
        RecommenderTokenReplacer $recommenderTokenReplacer,
        CampaignLeadDetails $campaignLeadDetails,
        TrackingHelper $trackingHelper,
        FocusModel $focusModel,
        Session $session,
        IntegrationHelper $integrationHelper,
        DynamicContentModel $dynamicContentModel,
        EntityManager $entityManager,
        NotificationModel $notificationModel,
        AbstractNotificationApi $notificationApi
    ) {
        $this->leadModel             = $leadModel;
        $this->emailModel            = $emailModel;
        $this->campaignEventModel    = $eventModel;
        $this->sendEmailToUser       = $sendEmailToUser;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->campaignLeadDetails   = $campaignLeadDetails;
        $this->trackingHelper        = $trackingHelper;
        $this->focusModel            = $focusModel;
        $this->session               = $session;
        $this->integrationHelper     = $integrationHelper;

        $this->dynamicContentModel = $dynamicContentModel;
        $this->entityManager       = $entityManager;
        $this->notificationModel = $notificationModel;
        $this->notificationApi = $notificationApi;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD             => ['onCampaignBuild', 0],
            RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION    => [
                ['onCampaignTriggerActionSendNotification', 2],
            ],
            RecommenderEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],

        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {


        $integration = $this->integrationHelper->getIntegrationObject('OneSignal');
        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            $event->addAction(
                'recommender.send_notification',
                [
                    'label'            => 'mautic.recommender.notification.campaign.event.send',
                    'description'      => 'mautic.recommender.notification.campaign.event.send',
                    'eventName'        => RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                    'formType'         => RecommenderNotificationSendType::class,
                    'formTypeOptions'  => ['update_select' => 'campaignevent_properties_notification'],
                    'formTheme'        => 'MauticRecommenderBundle:FormTheme\NotificationSendList',
                    'timelineTemplate' => 'MauticNotificationBundle:SubscribedEvents\Timeline:index.html.php',
                    'channel'         => 'notification',
                    'channelIdField'  => 'notification',
                ]
            );
        }
    }

    /**
     * Triggers the action which sends email to contact.
     *
     * @param CampaignExecutionEvent $event
     *
     * @return CampaignExecutionEvent|null
     */
    public function onCampaignTriggerActionSendRecommenderEmail(CampaignExecutionEvent $event)
    {

        if (!$event->checkContext('recommender.email.send')) {
            return;
        }

        $leadCredentials = $event->getLeadFields();

        if (empty($leadCredentials['email'])) {
            return $event->setFailed('Contact does not have an email');
        }

        $config     = $event->getConfig();
        $emailId    = $config['email']['email'];
        $email      = $this->emailModel->getEntity($emailId);
        $campaignId = $event->getEvent()['campaign']['id'];
        $leadId     = $event->getLead()->getId();

        if (!$email || !$email->isPublished()) {
            return $event->setFailed('Email not found or published');
        }
        $options = [
            'source'        => ['campaign.event', $event->getEvent()['id']],
            'return_errors' => true,
            'dnc_as_error'  => true,
        ];

        $event->setChannel('recommender-email', $emailId);
        $email->setCustomHtml(
            $this->recommenderTokenReplacer->replaceTokensFromContent(
                $email->getCustomHtml(),
                $this->getOptionsBasedOnRecommendationsType($config['type'], $campaignId, $leadId)
            )
        );

        // check if cart has some items
        if (!$this->recommenderTokenReplacer->hasItems()) {
            return $event->setFailed(
                'No recommender results for this contact #'.$leadCredentials['id'].' and  email #'.$email->getId()
            );
        }

        $email->setSubject($this->recommenderTokenReplacer->getRecommenderGenerator()->replaceTagsFromContent($email->getSubject()));

        $result = $this->emailModel->sendEmail($email, $leadCredentials, $options);
        if (is_array($result)) {
            $errors = implode('<br />', $result);

            // Add to the metadata of the failed event
            $result = [
                'result' => false,
                'errors' => $errors,
            ];
        } elseif (true !== $result) {
            $result = [
                'result' => false,
                'errors' => $result,
            ];
        } else {
            $result = [
                'id' => $email->getId(),
            ];
        }

        return $event->setResult($result);
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
     * @param CampaignExecutionEvent $event
     */
    private function setResults(CampaignExecutionEvent $event)
    {
       return $event->setResult([
            'type'       => $event->getConfig()['type'],
            'campaignId' => $event->getEvent()['campaign']['id'],
        ]);
    }

    /**
     * @param     $config
     * @param int $campaignId
     * @param int $leadId
     *
     * @return array
     */
    private function getOptionsBasedOnRecommendationsType(array $config, $campaignId, $leadId)
    {
        $options = [];

        $type = $config['type'];

        switch ($type) {
            case 'abandoned_cart':
                $seconds = $this->campaignLeadDetails->getDiffSecondsFromAddedTime($campaignId, $leadId);
                $options = $this->getAbandonedCartOptions(1, $seconds);
                break;
        }

        return $options;
    }

    /**
     * @param $cartMinAge
     * @param $cartMaxAge
     *
     * @return array
     */
    private function getAbandonedCartOptions($cartMinAge, $cartMaxAge)
    {
        return [
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
                                    "detail-view"   => [
                                        "enabled" => false,
                                    ],
                                    "cart-addition" => [
                                        "enabled" => true,
                                        "weight"  => 1.0,
                                        "min-age" => $cartMinAge,
                                        "max-age" => $cartMaxAge+60,
                                    ],
                                ],
                                "filter-purchased-max-age" => $cartMaxAge,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerCondition(CampaignExecutionEvent $event)
    {
        $lead = $event->getLead();

        if (!$lead || !$lead->getId()) {
            return $event->setResult(false);
        }
    }

    /**
     * @param CampaignExecutionEvent $event
     *
     * @return CampaignExecutionEvent
     */
    public function onCampaignTriggerActionSendNotification(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('recommender.send_notification')) {
            return;
        }

        $lead = $event->getLead();

        if ($this->leadModel->isContactable($lead, 'notification') !== DNC::IS_CONTACTABLE) {
            return $event->setFailed('mautic.notification.campaign.failed.not_contactable');
        }
        $notificationId = (int) $event->getConfig()['notification'];

        /** @var \Mautic\NotificationBundle\Entity\Notification $notification */
        $notification = $this->notificationModel->getEntity($notificationId);

        if ($notification && $notification->getId() !== $notificationId) {
            return $event->setFailed('mautic.notification.campaign.failed.missing_entity');
        }
        if (!$notification->getIsPublished()) {
            return $event->setFailed('mautic.notification.campaign.failed.unpublished');
        }

        // If lead has subscribed on multiple devices, get all of them.
        /** @var \Mautic\NotificationBundle\Entity\PushID[] $pushIDs */
        $pushIDs = $lead->getPushIDs();

        $playerID = [];

        foreach ($pushIDs as $pushID) {
            // Skip mobile PushIDs if this is a non-mobile event
            if ($pushID->isMobile() == true) {
                continue;
            }

            $playerID[] = $pushID->getPushID();
        }

        if (empty($playerID)) {
            return $event->setFailed('mautic.notification.campaign.failed.not_subscribed');
        }

        $config     = $event->getConfig();
        $campaignId = $event->getEvent()['campaign']['id'];
        $leadId     = $event->getLead()->getId();
        // create token from data
        $this->recommenderTokenReplacer->getRecommenderToken()->setToken(['userId' => $leadId, 'limit' => 10]);
        $recommenderTagsReplacer = new RecommenderTagsReplacer($this->recommenderTokenReplacer, $this->recommenderTokenReplacer->getRecommenderToken(), $this->getOptionsBasedOnRecommendationsType($config['type'], $campaignId, $leadId));
        $notification->setMessage($recommenderTagsReplacer->replaceTags($notification->getMessage()));
        // check if cart has some items
        if ($this->recommenderTokenReplacer->hasItems() === false) {
            return $event->setFailed(
                'No recommender results for this contact #'.$leadId.' and  notification #'.$notificationId
            );
        }

        $notification->setHeading($recommenderTagsReplacer->replaceTags($notification->getHeading()));
        $notification->setUrl($recommenderTagsReplacer->replaceTags($notification->getUrl()));
        if (method_exists($notification, 'getActionButtonUrl1') && $notification->getActionButtonUrl1()) {
            $notification->setActionButtonUrl1($recommenderTagsReplacer->replaceTags($notification->getActionButtonUrl1()));
        }
        if (method_exists($notification, 'getActionButtonUrl2') && $notification->getActionButtonUrl2()) {
            $notification->setActionButtonUrl2($recommenderTagsReplacer->replaceTags($notification->getActionButtonUrl2()));
        }

        if ($url = $notification->getUrl()) {
            $url = $this->notificationApi->convertToTrackedUrl(
                $url,
                [
                    'notification' => $notification->getId(),
                    'lead'         => $lead->getId(),
                ],
                $notification
            );
        }

        /** @var TokenReplacementEvent $tokenEvent */
        $tokenEvent = $this->dispatcher->dispatch(
            NotificationEvents::TOKEN_REPLACEMENT,
            new TokenReplacementEvent(
                $notification->getMessage(),
                $lead,
                ['channel' => ['recommender-notification', $notification->getId()]]
            )
        );

        /** @var NotificationSendEvent $sendEvent */
        $sendEvent = $this->dispatcher->dispatch(
            NotificationEvents::NOTIFICATION_ON_SEND,
            new NotificationSendEvent($tokenEvent->getContent(), $notification->getHeading(), $lead)
        );
        // prevent rewrite notification entity
        $sendNotification = clone $notification;
        $sendNotification->setUrl($url);
        $sendNotification->setMessage($sendEvent->getMessage());
        $sendNotification->setHeading($sendEvent->getHeading());

        $response = $this->notificationApi->sendNotification(
            $playerID,
            $sendNotification,
            $notification
        );

        $event->setChannel('notification', $notification->getId());

        // If for some reason the call failed, tell mautic to try again by return false
        if ($response->code !== 200) {
            return $event->setResult(false);
        }

        $this->notificationModel->createStatEntry($notification, $lead, 'campaign.event', $event->getEvent()['id']);
        $this->notificationModel->getRepository()->upCount($notificationId);

        $result = [
            'status'  => 'mautic.notification.timeline.status.delivered',
            'type'    => 'mautic.notification.notification',
            'id'      => $notification->getId(),
            'name'    => $notification->getName(),
            'heading' => $sendEvent->getHeading(),
            'content' => $sendEvent->getMessage(),
        ];

        $event->setResult($result);
    }

}
