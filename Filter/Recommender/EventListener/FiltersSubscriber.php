<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender\EventListener;

use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticRecommenderBundle\Filter\EventDecorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\SegmentFilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;

class FiltersSubscriber extends CommonSubscriber
{


    /**
     * @var SegmentFilterFactory
     */
    private $segmentFilterFactory;

    /**
     * FiltersSubscriber constructor.
     *
     * @param SegmentFilterFactory $segmentFilterFactory
     */
    public function __construct(SegmentFilterFactory $segmentFilterFactory)
    {

        $this->segmentFilterFactory = $segmentFilterFactory;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [

            RecommenderEvents::LIST_FILTERS_ON_FILTERING => [
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
            $this->segmentFilterFactory->applySegmentQuery($filter, $qb, 'mautic.recommender.filter.recommender.dictionary');
            $event->setFilteringStatus(true);
        }
    }

}
