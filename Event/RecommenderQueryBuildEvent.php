<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class RecommenderQueryBuildEvent extends CommonEvent
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var RecommenderToken
     */
    private $recommenderToken;

    /**
     * RecommenderQueryBuildEvent constructor.
     *
     * @param QueryBuilder     $queryBuilder
     * @param RecommenderToken $recommenderToken
     */
    public function __construct(QueryBuilder $queryBuilder, RecommenderToken  $recommenderToken)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->recommenderToken = $recommenderToken;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return RecommenderToken
     */
    public function getRecommenderToken()
    {
        return $this->recommenderToken;
    }
}
