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
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\RecommenderFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;

class AbandonedCartQueryBuilder extends RecommenderFilterQueryBuilder
{
    /** @var RandomParameterName */
    protected $parameterNameGenerator;

    /**
     * @var RecommenderClientModel
     */
    protected $clientModel;

    /**
     * BaseFilterQueryBuilder constructor.
     *
     * @param RandomParameterName    $randomParameterNameService
     * @param RecommenderClientModel $clientModel
     */
    public function __construct(RandomParameterName $randomParameterNameService, RecommenderClientModel  $clientModel)
    {
        $this->parameterNameGenerator = $randomParameterNameService;
        $this->clientModel            = $clientModel;
        parent::__construct($randomParameterNameService);
    }

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
        return 'mautic.recommender.query.builder.recommender.abandoned_cart';
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

        $leftJoinCondition = $tableAlias2.'.lead_id = '.$tableAlias.'.lead_id  AND '.$tableAlias2.'.id > '.$tableAlias.'.id AND '.$tableAlias2.'.item_id = '.$tableAlias.'.item_id AND (('.$tableAlias.'.item_id = '.$tableAlias2.'.item_id AND  '.$tableAlias2.'.event_id  = 3) OR  '.$tableAlias2.'.event_id = 4)';

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
        $subQueryBuilder->andWhere($tableAlias2.'.id IS NULL');

        $queryBuilder->addLogic($queryBuilder->expr()->exists($subQueryBuilder->getSQL()), $filter->getGlue());

        //$queryBuilder->setParametersPairs($parameters, $filterParameters);
        $this->setParameters($queryBuilder, $parameters, $filterParameters, $filter);

        return $queryBuilder;
    }
}
