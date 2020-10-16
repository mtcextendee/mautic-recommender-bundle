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
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\FilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventDateQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventValueQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemValueQueryBuilder;

class RecommenderDictionary
{
    const ALLOWED_TABLES = [
        'recommenders',
        'recommender_item',
        'recommender_item_property_value',
        'recommender_event_log',
        'recommender_event_log_property_value',
    ];

    /**
     * @var Fields
     */
    private $fields;

    /**
     * SegmentChoices constructor.
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
    }

    public function getDictionary()
    {
        $dictionary = [];
        foreach (self::ALLOWED_TABLES as $table) {
            $fields = $this->fields->getFields($table);
            foreach ($fields as $key => $field) {
                if (isset($field['decorator']['recommender']['type'])) {
                    $dictionary[$key] = [
                        'type'          => $field['decorator']['recommender']['type'],
                        'foreign_table' => isset($field['decorator']['recommender']['foreign_table']) ? $field['decorator']['recommender']['foreign_table'] : $table,
                        'field'         => isset($field['decorator']['key']) ? $field['decorator']['key'] : $key,
                    ];
                }
            }
        }

        return $dictionary;
    }
}
