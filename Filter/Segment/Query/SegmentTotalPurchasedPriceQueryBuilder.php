<?php
/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\RecommenderFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;

class SegmentTotalPurchasedPriceQueryBuilder extends RecommenderFilterQueryBuilder
{
    /**
     * @return string
     */
    public function getIdentificator()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public static function getServiceId()
    {
        return 'mautic.recommender.query.builder.total_purchased_price';
    }

    /** {@inheritdoc} */
    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter)
    {
        $filterOperator   = $filter->getOperator();
        $filterParameters = $filter->getParameterValue();
        if (is_array($filterParameters)) {
            $parameters = [];
            foreach ($filterParameters as $filterParameter) {
                $parameters[] = $this->generateRandomParameterName();
            }
        } else {
            $parameters = $this->generateRandomParameterName();
        }
        $filterParametersHolder = $filter->getParameterHolder($parameters);
        $tableAlias             = $this->generateRandomParameterName();
        $tableAlias2            = $this->generateRandomParameterName();

        $leftJoinCondition = sprintf(
            '%s.lead_id = %s.lead_id AND %s.event_id IN (3,4) AND %s.id > %s.id AND %s.item_id = %s.item_id',
            $tableAlias2,
            $tableAlias,
            $tableAlias2,
            $tableAlias2,
            $tableAlias,
            $tableAlias2,
            $tableAlias
        );

        $subQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
        $subQueryBuilder
            ->select('NULL')->from('recommender_event_log', $tableAlias)
            ->leftJoin($tableAlias, 'recommender_event_log', $tableAlias2,
                $leftJoinCondition
            )
            ->andWhere($tableAlias.'.event_id = 2')
            ->andWhere($tableAlias.'.'.$this->getIdentificator().' = l.id');

        if (!is_null($filter->getWhere())) {
            $subQueryBuilder->andWhere(str_replace(str_replace(MAUTIC_TABLE_PREFIX, '', $filter->getTable()).'.', $tableAlias.'.', $filter->getWhere()));
        }

        $expression = $subQueryBuilder->expr()->$filterOperator(
            $tableAlias.'.date_added',
            $filterParametersHolder
        );
        $subQueryBuilder->andWhere($expression);

        $queryBuilder->addLogic($queryBuilder->expr()->exists($subQueryBuilder->getSQL()), $filter->getGlue());

        //$queryBuilder->setParametersPairs($parameters, $filterParameters);
        $this->setParameters($queryBuilder, $parameters, $filterParameters, $filter);

        return $queryBuilder;
    }
}
