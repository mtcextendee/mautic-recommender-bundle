<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Expression\CompositeExpression;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\RecommenderFilterQueryBuilder;

class ItemQueryBuilder extends RecommenderFilterQueryBuilder
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
        return 'mautic.recommender.query.builder.recommender.item';
    }

    /** {@inheritdoc} */
    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter)
    {
        $filterOperator = $filter->getOperator();

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

        $tableAlias = $queryBuilder->getTableAlias($filter->getTable());

        if (!$tableAlias) {
            $tableAlias = $this->generateRandomParameterName();
            $queryBuilder->leftJoin('l', $filter->getTable(), $tableAlias, $tableAlias.'.'.$this->getIdentificator().' = l.item_id');
        }
        $subQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
        $subQueryBuilder
            ->select('NULL')->from($filter->getTable(), $tableAlias)
            ->andWhere($tableAlias.'.'.$this->getIdentificator().' = l.id');

        switch ($filterOperator) {
            case 'empty':
                $expression = new CompositeExpression(CompositeExpression::TYPE_OR,
                    [
                        $queryBuilder->expr()->isNull($tableAlias.'.'.$filter->getField()),
                        $queryBuilder->expr()->eq($tableAlias.'.'.$filter->getField(), $queryBuilder->expr()->literal('')),
                    ]
                );
                break;
            case 'notEmpty':
                $expression = new CompositeExpression(CompositeExpression::TYPE_AND,
                    [
                        $queryBuilder->expr()->isNotNull($tableAlias.'.'.$filter->getField()),
                        $queryBuilder->expr()->neq($tableAlias.'.'.$filter->getField(), $queryBuilder->expr()->literal('')),
                    ]
                );

                break;
            case 'neq':
                $expression = $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($tableAlias.'.'.$filter->getField()),
                    $queryBuilder->expr()->$filterOperator(
                        $tableAlias.'.'.$filter->getField(),
                        $filterParametersHolder
                    )
                );
                break;
            case 'startsWith':
            case 'endsWith':
            case 'gt':
            case 'eq':
            case 'gte':
            case 'like':
            case 'lt':
            case 'lte':
            case 'in':
            case 'between':   //Used only for date with week combination (EQUAL [this week, next week, last week])
            case 'regexp':
            case 'notRegexp': //Different behaviour from 'notLike' because of BC (do not use condition for NULL). Could be changed in Mautic 3.
                $expression = $queryBuilder->expr()->$filterOperator(
                    $tableAlias.'.'.$filter->getField(),
                    $filterParametersHolder
                );
                break;
            case 'notLike':
            case 'notBetween': //Used only for date with week combination (NOT EQUAL [this week, next week, last week])
            case 'notIn':
                $expression = $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->$filterOperator($tableAlias.'.'.$filter->getField(), $filterParametersHolder),
                    $queryBuilder->expr()->isNull($tableAlias.'.'.$filter->getField())
                );
                break;
            case 'notGt':
            case 'notLt':
                $expr       = strtolower(str_replace('not', '', $filterOperator));
                $expression = $subQueryBuilder->expr()->orX(
                    $subQueryBuilder->expr()->isNull($tableAlias.'.'.$filter->getField()),
                    $subQueryBuilder->expr()->$expr($tableAlias.'.'.$filter->getField(), $filterParametersHolder)
                );

                $subQueryBuilder->andWhere($expression);

                $queryBuilder->addLogic($queryBuilder->expr()->notExists($subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'multiselect':
            case '!multiselect':
                $operator    = 'multiselect' === $filterOperator ? 'regexp' : 'notRegexp';
                $expressions = [];
                foreach ($filterParametersHolder as $parameter) {
                    $expressions[] = $queryBuilder->expr()->$operator($tableAlias.'.'.$filter->getField(), $parameter);
                }

                $expression = $queryBuilder->expr()->andX($expressions);
                break;
            default:
                throw new \Exception('Dunno how to handle operator "'.$filterOperator.'"');
        }

        if (!in_array($filterOperator, ['notGt', 'notLt'])) {
            $queryBuilder->addLogic($expression, $filter->getGlue());
        }
        //$queryBuilder->setParametersPairs();
//        $queryBuilder->setParametersPairs($parameters, $filterParameters);
        $this->setParameters($queryBuilder, $parameters, $filterParameters, $filter);

        return $queryBuilder;
    }
}
