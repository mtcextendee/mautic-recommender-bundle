<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\LeadList;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListQueryBuilderGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Exception\PluginHandledFilterException;
use Mautic\LeadBundle\Segment\Exception\SegmentQueryException;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\SegmentFilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecommenderQueryBuilder
{
    /** @var EntityManager */
    private $entityManager;

    /** @var RandomParameterName */
    private $randomParameterName;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @var SegmentFilterFactory
     */
    private $segmentFilterFactory;


    /**
     * ContactSegmentQueryBuilder constructor.
     *
     * @param EntityManager            $entityManager
     * @param RandomParameterName      $randomParameterName
     * @param EventDispatcherInterface $dispatcher
     * @param SegmentFilterFactory     $segmentFilterFactory
     */
    public function __construct(EntityManager $entityManager, RandomParameterName $randomParameterName, EventDispatcherInterface $dispatcher, SegmentFilterFactory $segmentFilterFactory)
    {
        $this->entityManager       = $entityManager;
        $this->randomParameterName = $randomParameterName;
        $this->dispatcher          = $dispatcher;
        $this->segmentFilterFactory = $segmentFilterFactory;
    }

    /**
     * @param array
     *
     * @return QueryBuilder
     *
     * @throws SegmentQueryException
     */
    public function assembleContactsSegmentQueryBuilder($recombeeFilters)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        if ($connection instanceof MasterSlaveConnection) {
            // Prefer a slave connection if available.
            $connection->connect('slave');
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = new QueryBuilder($connection);

        $queryBuilder->select('l.id')->from(MAUTIC_TABLE_PREFIX.'recommender_item', 'l');
        $filters = [];
        foreach ($recombeeFilters as $filter) {
            $filters[] = $this->segmentFilterFactory->getContactSegmentFilter($filter, 'mautic.recommender.filter.recommender.dictionary');

        }
        /** @var ContactSegmentFilter $filter */
        foreach ($filters as $filter) {
            try {
                $this->dispatchPluginFilteringEvent($filter, $queryBuilder);
            } catch (PluginHandledFilterException $exception) {
                continue;
            }

            $queryBuilder = $filter->applyQuery($queryBuilder);
        }
        return $queryBuilder;
    }

    /**
     * @param ContactSegmentFilter $filter
     * @param QueryBuilder         $queryBuilder
     *
     * @throws PluginHandledFilterException
     */
    private function dispatchPluginFilteringEvent(ContactSegmentFilter $filter, QueryBuilder $queryBuilder)
    {
        if ($this->dispatcher->hasListeners(RecommenderEvents::LIST_FILTERS_ON_FILTERING)) {
            //  This has to run for every filter
            $filterCrate = $filter->contactSegmentFilterCrate->getArray();

            $alias = $this->generateRandomParameterName();
            $event = new LeadListFilteringEvent($filterCrate, null, $alias, $filterCrate['operator'], $queryBuilder, $this->entityManager);
            $this->dispatcher->dispatch(RecommenderEvents::LIST_FILTERS_ON_FILTERING, $event);
            if ($event->isFilteringDone()) {
                $queryBuilder->addLogic($event->getSubQuery(), $filter->getGlue());

                throw new PluginHandledFilterException();
            }
        }
    }

    /**
     * Generate a unique parameter name.
     *
     * @return string
     */
    private function generateRandomParameterName()
    {
        return $this->randomParameterName->generateRandomParameterName();
    }

}
