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
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Filter\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\Decorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
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
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var Decorator
     */
    private $decorator;

    /**
     * ContactSegmentQueryBuilder constructor.
     *
     * @param EntityManager            $entityManager
     * @param RandomParameterName      $randomParameterName
     * @param EventDispatcherInterface $dispatcher
     * @param FilterFactory            $filterFactory
     * @param Decorator                $decorator
     */
    public function __construct(
        EntityManager $entityManager,
        RandomParameterName $randomParameterName,
        EventDispatcherInterface $dispatcher,
        FilterFactory $filterFactory,
        Decorator $decorator
    ) {
        $this->entityManager       = $entityManager;
        $this->randomParameterName = $randomParameterName;
        $this->dispatcher          = $dispatcher;
        $this->filterFactory       = $filterFactory;
        $this->decorator           = $decorator;
    }

    /**
     * @param RecommenderToken $recommenderToken
     *
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
        $recombeeFilters = $recommenderToken->getRecommender()->getFilters();
        foreach ($recombeeFilters as $filter) {
            $filter       = $this->filterFactory->getContactSegmentFilter($filter, $this->decorator);
            $queryBuilder = $filter->applyQuery($queryBuilder);
        }
           /* $aliases = $queryBuilder->getAllTableAliases();
            if (isset($aliases['recommender_event_log'])) {
                foreach ($aliases['recommender_event_log'] as $aliase) {
                    $queryBuilder->andWhere($queryBuilder->expr()->eq($aliase.'.lead_id', (int) $recommenderToken->getUserId()));
                }
            }*/

        $queryBuilder->groupBy('l.item_id');
        $queryBuilder->setMaxResults($recommenderToken->getLimit());

        return $queryBuilder;
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
