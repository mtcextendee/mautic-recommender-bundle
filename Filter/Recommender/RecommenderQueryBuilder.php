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
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Enum\FiltersEnum;
use MauticPlugin\MauticRecommenderBundle\Event\RecommenderQueryBuildEvent;
use MauticPlugin\MauticRecommenderBundle\Filter\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\Decorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\RecommenderOrderBy;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecommenderQueryBuilder
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var Decorator
     */
    private $decorator;

    /**
     * @var RecommenderOrderBy
     */
    private $recommenderOrderBy;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * ContactSegmentQueryBuilder constructor.
     */
    public function __construct(
        EntityManager $entityManager,
        RandomParameterName $randomParameterName,
        FilterFactory $filterFactory,
        Decorator $decorator,
        RecommenderOrderBy $recommenderOrderBy,
        EventDispatcherInterface $dispatcher
    ) {
        $this->entityManager       = $entityManager;
        $this->filterFactory       = $filterFactory;
        $this->decorator           = $decorator;
        $this->recommenderOrderBy  = $recommenderOrderBy;
        $this->dispatcher          = $dispatcher;
    }

    /**
     * @return QueryBuilder
     */
    public function assembleContactQueryBuilder(RecommenderToken $recommenderToken)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        if ($connection instanceof MasterSlaveConnection) {
            // Prefer a slave connection if available.
            $connection->connect('slave');
        }

        $queryBuilder = new QueryBuilder($connection);
        $queryBuilder->select('l.item_id as id')->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', 'l');
        $queryBuilder->innerJoin('l', MAUTIC_TABLE_PREFIX.'recommender_item', 'ri', 'ri.id = l.item_id');

        if (!ArrayHelper::getValue('includeDisabledItems', $recommenderToken->getRecommender()->getProperties())) {
            $queryBuilder->andWhere('ri.active=1');
        }

        $recommenderToken->reset();

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_BUILD_QUERY)) {
            $recommenderQuieryBuildEvent = new RecommenderQueryBuildEvent($queryBuilder, $recommenderToken);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_BUILD_QUERY, $recommenderQuieryBuildEvent);
        }

        $recombeeFilters = $recommenderToken->getFilters();
        foreach ($recombeeFilters as $filter) {
            $filter       = $this->filterFactory->getContactSegmentFilter($filter, $this->decorator);
            $queryBuilder = $filter->applyQuery($queryBuilder);
        }

        if (FiltersEnum::CUSTOM === $recommenderToken->getRecommender()->getFilterTarget()) {
            $this->setCustomOrderBy($queryBuilder, $recommenderToken->getRecommender());
        }

        $queryBuilder->groupBy('l.item_id');
        $queryBuilder->setMaxResults($recommenderToken->getLimit());

        return $queryBuilder;
    }

    private function setCustomOrderBy(QueryBuilder $queryBuilder, Recommender $recommender)
    {
        $tableorder = $recommender->getTableOrder();
        if (empty($tableorder['column'])) {
            return;
        }
        $orderBy = $this->recommenderOrderBy->getDictionary($queryBuilder, $tableorder['column']);

        if (!empty($tableorder['function'])) {
            $orderBy = $tableorder['function'].'('.$orderBy.')';
        }
        $queryBuilder->orderBy($orderBy, $tableorder['direction']);
    }
}
