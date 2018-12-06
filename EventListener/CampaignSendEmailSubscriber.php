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
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\Model\EmailModel;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEmailSendType;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

class CampaignSendEmailSubscriber extends CommonSubscriber
{
    /**
     * @var EmailModel
     */
    protected $emailModel;

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;


    /**
     * @param EmailModel              $emailModel
     * @param RecommenderTokenReplacer   $recommenderTokenReplacer
     */
    public function __construct(
        EmailModel $emailModel,
        RecommenderTokenReplacer $recommenderTokenReplacer
    ) {
        $this->emailModel            = $emailModel;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD             => ['onCampaignBuild', 0],
            RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION    => [
                ['onCampaignTriggerActionSendRecommenderEmail', 0],
            ],
        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addAction(
            'recommender.email.send',
            [
                'label'           => 'mautic.recommender.email.campaign.event.send',
                'description'     => 'mautic.recommender.email.campaign.event.send.desc',
                'eventName'       => RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                'formType'        => RecommenderEmailSendType::class,
                'formTypeOptions' => ['update_select' => 'campaignevent_properties_email'],
                'channel'         => 'email',
                'channelIdField'  => 'email',
            ]
        );
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
}
