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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadMergeEvent;
use Mautic\LeadBundle\Event\LeadTimelineEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;

/**
 * Class LeadSubscriber.
 */
class LeadSubscriber extends CommonSubscriber
{
     /**
     * @var integrationHelper
     */
    protected $integrationHelper;

    /**
     * LeadSubscriber constructor.
     *
     * @param FormModel $formModel
     * @param PageModel $pageModel
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {        
        $this->integrationHelper      = $integrationHelper;           
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::TIMELINE_ON_GENERATE => ['onTimelineGenerate', 0],
            LeadEvents::LEAD_POST_MERGE      => ['onLeadMerge', 0],
        ];
    }

    /**
     * Compile events for the lead timeline.
     *
     * @param LeadTimelineEvent $event
     */
    public function onTimelineGenerate(LeadTimelineEvent $event)
    {

        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration){
            return;
        }
        $integrationSettings = $integration->getIntegrationSettings();
        if (!$integration || $integrationSettings->getIsPublished() === false) {
            return;
        }

        // Set available event types
        $eventTypeKey  = 'recommender.event';
        $eventTypeName = $this->translator->trans('mautic.plugin.recommender.event.timeline_event');
        $event->addEventType($eventTypeKey, $eventTypeName);        

        if (!$event->isApplicable($eventTypeKey)) {
            return;
        }

        /** @var \Mautic\FormBundle\Entity\SubmissionRepository $submissionRepository */
        $eventLogRepository = $this->em->getRepository('MauticRecommenderBundle:EventLog');
        $rows               = $eventLogRepository->getTimeLineEvents($event->getLead(), $event->getQueryOptions());

        // Add total to counter
        $event->addToCounter($eventTypeKey, $rows);

        if (!$event->isEngagementCount()) {
            // Add the submissions to the event array
            foreach ($rows['results'] as $row) {
                $eventLogEntity   = $eventLogRepository->getEntity($row['id']);
                
                $event->addEvent(
                    [
                        'event'      => $eventTypeKey,
                        'eventId'    => $eventTypeKey.$row['id'],
                        'eventLabel' => $this->getLabel($eventLogEntity),
                        'eventType' => $eventTypeName,
                        'timestamp' => $row['date_added'],                   
                        'icon'            => 'fa-shopping-bag',
                        'contactId'       => $row['lead_id'],
                    ]
                );
            }
        }
    }

    private function getLabel($eventLogEntity){
        return $this->translator->trans(
            'mautic.plugin.recommender.event.timeline_event.label',
            [
                '%event_name%' => $eventLogEntity->getEvent() ? $eventLogEntity->getEvent()->getName() : 'deleted',
                '%item_id%' => $eventLogEntity->getItem() ? $eventLogEntity->getItem()->getId() : 'deleted'
            ]
        );
    }

    /**
     * @param LeadMergeEvent $event
     */
    public function onLeadMerge(LeadMergeEvent $event)
    {
        //$this->em->getRepository('MauticFormBundle:Submission')->updateLead($event->getLoser()->getId(), $event->getVictor()->getId());
    }
}
