<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator;


use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Filter\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\FilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventDateQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventValueQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemValueQueryBuilder;

class RecommenderOrderBy
{
    CONST ALLOWED_TABLES = ['recommender_event_log', 'recommender_event_log_property_value'];

    /**
     * @var Fields
     */
    private $fields;


    /**
     * SegmentChoices constructor.
     *
     * @param Fields $fields
     */
    public function __construct(Fields $fields)
    {

        $this->fields = $fields;
    }

    public function getDictionary(QueryBuilder $queryBuilder, $column)
    {

        $dictionary = [];
        foreach (self::ALLOWED_TABLES as $table) {
            $fields = $this->fields->getFields($table);
            foreach ($fields as $key => $field) {
                if ($column != $key) {
                    continue;
                }
                $tableFromDecorator = isset($field['decorator']['recommender']['foreign_table']) ? $field['decorator']['recommender']['foreign_table'] : $table;
                $keyFromDecorator = isset($field['decorator']['recommender']['key']) ? $field['decorator']['recommender']['key'] : $key;

                // Order By from decorator
                if (isset($field['decorator']['recommender']['orderBy'])) {
                    return $field['decorator']['recommender']['orderBy'];
                }

                //Order by default by column
                $tableAlias = $queryBuilder->getTableAlias($tableFromDecorator);

                return $tableAlias.'.'.$keyFromDecorator;
            }

        }
    }
}

