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
use Mautic\DynamicContentBundle\DynamicContentEvents;
use Mautic\DynamicContentBundle\Entity\DynamicContent;
use Mautic\DynamicContentBundle\Model\DynamicContentModel;
use Mautic\EmailBundle\Model\EmailModel;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentRemoveType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentType;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEmailSendType;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

class CampaignDynamicContentSubscriber extends CommonSubscriber
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
     * @param DynamicContentModel      $dynamicContentModel
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     */
    public function __construct(
        DynamicContentModel $dynamicContentModel,
        RecommenderTokenReplacer $recommenderTokenReplacer
    ) {
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->dynamicContentModel      = $dynamicContentModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD             => ['onCampaignBuild', 0],
            RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION => [
                ['onCampaignTriggerActionDynamiContent', 0],
                ['onCampaignTriggerActionDynamiContentRemove', 1],
            ],
            DynamicContentEvents::TOKEN_REPLACEMENT       => ['onDynamicContentTokenReplacement', 200],
        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addAction(
            'recommender.dynamic.content',
            [
                'label'           => 'mautic.recommender.dynamic.content.campaign.event',
                'description'     => 'mautic.recommender.dynamic.content.campaign.event.desc',
                'eventName'       => RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                'formType'        => RecommenderDynamicContentType::class,
                'formTypeOptions' => ['update_select' => 'campaignevent_properties_dynamicContent'],
                'channel'         => 'dynamicContent',
                'channelIdField'  => 'dynamic_content',
            ]
        );

        $event->addAction(
            'recommender.dynamic.content.remove',
            [
                'label'     => 'mautic.recommender.dynamic.content.remove.campaign.event',
                'eventName' => RecommenderEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                'formType'  => RecommenderDynamicContentRemoveType::class,
            ]
        );
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerActionDynamiContent(CampaignExecutionEvent $event)
    {

        if (!$event->checkContext('recommender.dynamic.content')) {
            return;
        }

        $slot             = $event->getConfig()['slot'];
        $dynamicContentId = (int) $event->getConfig()['dynamic_content'];
        $lead             = $event->getLead();

        if (!$dynamicContentId) {
            return $event->setResult('Dynamic COntent ID #'.$dynamicContentId.' doesn\'t exist.');
        }
        /** @var DynamicContent $dwc */
        $dwc = $this->dynamicContentModel->getEntity($dynamicContentId);;

        if ($dwc instanceof DynamicContent) {
            // Use translation if available
            list($ignore, $dwc) = $this->dynamicContentModel->getTranslatedEntity($dwc, $lead);

            if ($slot) {
                $this->dynamicContentModel->setSlotContentForLead($dwc, $lead, $slot);
            }

            $this->dynamicContentModel->createStatEntry($dwc, $lead, $slot);
            $event->setChannel('recommender-dynamic-content', $dynamicContentId);
            $result = [
                'type'       => $event->getConfig()['type'],
                'campaignId' => $event->getEvent()['campaign']['id'],
                'slot'       => $slot,
            ];

            return $event->setResult($result);
        }

        $this->setResults($event);

        return $event->setResult(array_merge($event->getResult(), ['slot' => $slot]));
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    private function setResults(CampaignExecutionEvent $event)
    {
        return $event->setResult(
            [
                'type'       => $event->getConfig()['type'],
                'campaignId' => $event->getEvent()['campaign']['id'],
            ]
        );
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerActionDynamiContentRemove(CampaignExecutionEvent $event)
    {

        if (!$event->checkContext('recommender.dynamic.content.remove')) {
            return;
        }

        $slot = $event->getConfig()['slot'];
        $lead = $event->getLead();

        $qb = $this->em->getConnection()->createQueryBuilder();
        $qb->delete(MAUTIC_TABLE_PREFIX.'dynamic_content_lead_data')
            ->andWhere($qb->expr()->eq('slot', ':slot'))
            ->andWhere($qb->expr()->eq('lead_id', ':lead_id'))
            ->setParameter('slot', $slot)
            ->setParameter('lead_id', $lead->getId())
            ->execute();

        $event->setChannel('recommender-dynamic-content');

        return $this->setResults($event);
    }

    /**
     * @param TokenReplacementEvent $event
     */
    public function onDynamicContentTokenReplacement(TokenReplacementEvent $event)
    {
        $clickthrough = $event->getClickthrough();
        $slot         = $clickthrough['slot'];
        $leadId       = $clickthrough['lead'];
        // Find last added campaign metadata
        $q = $this->em->getConnection()->createQueryBuilder();
        $q->select('e.metadata')
            ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'e')
            ->where(
                $q->expr()->eq('e.channel', ':channel'),
                $q->expr()->eq('e.lead_id', ':lead_id'),
                $q->expr()->like('e.metadata', ':search')
            )
            ->setParameter('channel', 'recommender-dynamic-content')
            ->setParameter('lead_id', $leadId)
            ->setParameter('search', '%"slot"%"'.$slot.'"%')
            ->orderBy('e.id', 'DESC')
            ->getMaxResults(1);
        if ($metadata = $q->execute()->fetchColumn()) {
            $metadata   = unserialize($metadata);
            $campaignId = $metadata['campaignId'];
            $content    = $event->getContent();
            $this->recommenderTokenReplacer->getRecommenderToken()->setConfig(
                $leadId,
                'campaign',
                $campaignId,
                [],
                $content
            );
            $event->setContent($this->recommenderTokenReplacer->getReplacedContent());
        }
    }
}
