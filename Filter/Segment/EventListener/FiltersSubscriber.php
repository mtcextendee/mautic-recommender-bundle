<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener;

use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticRecommenderBundle\Filter\EventDecorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;

class FiltersSubscriber extends CommonSubscriber
{

    /**
     * @var FilterFactory
     */
    private $segmentFilterFactory;

    /**
     * @var Choices
     */
    private $choices;

    /**
     * FiltersSubscriber constructor.
     *
     * @param FilterFactory $segmentFilterFactory
     * @param Choices       $choices
     */
    public function __construct(FilterFactory $segmentFilterFactory, Choices $choices)
    {

        $this->segmentFilterFactory = $segmentFilterFactory;
        $this->choices = $choices;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => [
                ['onListFiltersGenerate', 0],
            ],

            LeadEvents::LIST_FILTERS_ON_FILTERING => [
                ['onListFiltersFiltering', 0],
            ],
        ];
    }

    /**
     * @param LeadListFilteringEvent $event
     */
    public function onListFiltersFiltering(LeadListFilteringEvent $event)
    {
        $qb     = $event->getQueryBuilder();
        $filter = $event->getDetails();
        if (false !== strpos($filter['object'], 'recommender')) {
            $this->segmentFilterFactory->applySegmentQuery($filter, $qb);
            $event->setFilteringStatus(true);
        }
    }


    /**
     * @param BuildJsEvent $event
     */
    public function onListFiltersGenerate(LeadListFiltersChoicesEvent $event)
    {
        $this->choices->addChoices($event, 'recommender_event');
    }
}
