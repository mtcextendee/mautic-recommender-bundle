<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Token;

use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Segment\RandomParameterName;
use MauticPlugin\MauticRecommenderBundle\Filter\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderPropertyModel;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class ContextToken
{
    /**
     * @var RandomParameterName
     */
    private $randomParameterName;

    /**
     * @var RecommenderPropertyModel
     */
    private $propertyModel;

    /**
     * @var DateTimeHelper
     */
    private $dateTimeHelper;

    /**
     * @var RecommenderEventModel
     */
    private $eventModel;

    public function __construct(RandomParameterName $randomParameterName, RecommenderPropertyModel $propertyModel, RecommenderEventModel $eventModel)
    {
        $this->randomParameterName = $randomParameterName;
        $this->propertyModel       = $propertyModel;
        $this->dateTimeHelper      = new DateTimeHelper();
        $this->eventModel          = $eventModel;
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function findValueFromContext($filter, QueryBuilder $queryBuilder, RecommenderToken $recommenderToken)
    {
        if (!$recommenderToken->getUserId()) {
            return '';
        }
        $contextParts =  explode(' ', str_replace(['{', '}'], '', $filter));

        if (count($contextParts) < 1) {
            return '';
        }

        $propertyName = str_replace('context_', '', $contextParts[0]);
        if (!$property = $this->propertyModel->getRepository()->findOneBy(['name' => $propertyName])) {
            return '';
        }

        array_shift($contextParts);

        $contextQuery = implode(' ', $contextParts);

        $contextParts = explode('AND', $contextQuery);

        $subQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();

        $tableAlias  = $this->randomParameterName->generateRandomParameterName();
        $tableAlias2 = $this->randomParameterName->generateRandomParameterName();

        $expressions = ['=' => 'eq', '>' => 'gt', '<' => 'lt'];
        foreach ($contextParts as $contextPart) {
            foreach ($expressions as $expression=>$queryBuilderExpression) {
                $expressionParts = explode($expression, $contextPart);
                if (2 === count($expressionParts)) {
                    switch (trim($expressionParts[0])) {
                        case 'date_added':
                            $dateAddedParam = $this->randomParameterName->generateRandomParameterName();
                            $this->dateTimeHelper->setDateTime($expressionParts[1]);
                            $subQueryBuilder->andWhere(
                                $subQueryBuilder->expr()->$queryBuilderExpression($tableAlias.'.date_added', ':'.$dateAddedParam)
                            );
                            $subQueryBuilder->setParameter($dateAddedParam, $this->dateTimeHelper->toUtcString());

                            break;
                        case 'event':
                            if ($expressionEvent = $this->eventModel->getRepository()->findOneBy(['name' => $expressionParts[1]])) {
                                $subQueryBuilder->andWhere(
                                    $subQueryBuilder->expr()->$queryBuilderExpression($tableAlias.'.event_id', $expressionEvent->getId())
                                );
                            }
                            break;
                    }
                }
            }
        }

        $subQueryBuilder
            ->select($tableAlias2.'.value')->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', $tableAlias)
            ->innerJoin(
                $tableAlias,
                MAUTIC_TABLE_PREFIX.'recommender_item_property_value',
                $tableAlias2,
                $tableAlias.'.item_id = '.$tableAlias2.'.item_id'
            )
            ->andWhere($tableAlias.'.lead_id = '.$recommenderToken->getUserId())
            ->andWhere($tableAlias2.'.property_id = '.$property->getId())
            ->orderBy('COUNT('.$tableAlias2.'.value)', 'DESC')
            ->groupBy($tableAlias2.'.value')
            ->setMaxResults(1);
        SqlQuery::debugQuery($subQueryBuilder);

        return $subQueryBuilder->execute()->fetchColumn(0);
    }
}
