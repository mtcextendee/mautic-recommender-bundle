<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Query;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;

class PropertyFilterQueryBuilder extends BaseFilterQueryBuilder
{

    /**
     * @return string
     */
    private function filterField()
    {
        return 'value';
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

            $relTable = $this->generateRandomParameterName();
            $queryBuilder->leftJoin('l', $filter->getRelationJoinTable(), $relTable, $relTable.'.'.$this->getIdentificator().' = l.id');
            $queryBuilder->leftJoin($relTable, $filter->getTable(), $tableAlias, $tableAlias.'.'.$filter->getRelationJoinTableField().' = '.$relTable.'.id'
            )
                ->andWhere($tableAlias.'.property_id = '.$filter->getField());
        }
        switch ($filterOperator) {
            case 'empty':
                $expression = new CompositeExpression(CompositeExpression::TYPE_OR,
                    [
                        $queryBuilder->expr()->isNull($tableAlias.'.'.$this->filterField()),
                        $queryBuilder->expr()->eq($tableAlias.'.'.$this->filterField(), $queryBuilder->expr()->literal('')),
                    ]
                );
                break;
            case 'notEmpty':
                $expression = new CompositeExpression(CompositeExpression::TYPE_AND,
                    [
                        $queryBuilder->expr()->isNotNull($tableAlias.'.'.$this->filterField()),
                        $queryBuilder->expr()->neq($tableAlias.'.'.$this->filterField(), $queryBuilder->expr()->literal('')),
                    ]
                );

                break;
            case 'neq':
                $expression = $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($tableAlias.'.'.$this->filterField()),
                    $queryBuilder->expr()->$filterOperator(
                        $tableAlias.'.'.$this->filterField(),
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
                    $tableAlias.'.'.$this->filterField(),
                    $filterParametersHolder
                );
                break;
            case 'notLike':
            case 'notBetween': //Used only for date with week combination (NOT EQUAL [this week, next week, last week])
            case 'notIn':
                $expression = $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->$filterOperator($tableAlias.'.'.$this->filterField(), $filterParametersHolder),
                    $queryBuilder->expr()->isNull($tableAlias.'.'.$this->filterField())
                );
                break;
            case 'multiselect':
            case '!multiselect':
                $operator    = $filterOperator === 'multiselect' ? 'regexp' : 'notRegexp';
                $expressions = [];
                foreach ($filterParametersHolder as $parameter) {
                    $expressions[] = $queryBuilder->expr()->$operator($tableAlias.'.'.$this->filterField(), $parameter);
                }

                $expression = $queryBuilder->expr()->andX($expressions);
                break;
            default:
                throw new \Exception('Dunno how to handle operator "'.$filterOperator.'"');
        }

        $queryBuilder->addLogic($expression, $filter->getGlue());

        $queryBuilder->setParametersPairs($parameters, $filterParameters);

        return $queryBuilder;
    }
}

