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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\LeadEvents;
use Mautic\PageBundle\Event as Events;
use Mautic\LeadBundle\Event\LeadEvent;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;


/**
 * Class LeadSubscriber.
 */
class LeadSubscriber extends CommonSubscriber
{

    /**
     * @var RecommenderHelper
     */
    protected $recommenderHelper;

    /**
     * @var ApiCommands
     */
    private $apiCommands;


    /**
     * PageSubscriber constructor.
     *
     * @param RecommenderHelper $recommenderHelper
     * @param ApiCommands    $apiCommands
     */
    public function __construct(
        RecommenderHelper $recommenderHelper,
        ApiCommands $apiCommands
    ) {
        $this->recommenderHelper = $recommenderHelper;
        $this->apiCommands    = $apiCommands;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_POST_SAVE => ['onLeadNewUpdate', 0],
        ];
    }


    /**
     * @param LeadEvent $event
     */
    public function onLeadNewUpdate(LeadEvent $event)
    {
        $lead = $event->getLead();

        if (!$lead instanceof Lead) {
            return;
        }

        if ($leadId = $lead->getId()) {
            $changes = $lead->getChanges(true);
            if (empty($changes) || empty($changes['fields'])) {
                return;
            }
            $properties = [];
            $properties[$leadId]['id'] = $leadId;
            foreach ($changes['fields'] as $property => $values) {
                if (empty($values[1])) {
                    continue;
                }
                $properties[$leadId][$property] = $values[1];
            }
            $this->apiCommands->ImportUser($properties);
        }
    }
}
