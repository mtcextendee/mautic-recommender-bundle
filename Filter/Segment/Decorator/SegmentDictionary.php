<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator;

use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\BaseFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\ItemValueQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentAbandonedCartQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventDateQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventValueQueryBuilder;

class SegmentDictionary
{
    const ALLOWED_TABLES = [
        'recommenders',
        'recommender_event_log',
        'recommender_event_log_property_value',
        'recommender_item',
        'recommender_item_property_value',
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
                switch ($table) {
                    case 'recommenders':
                        switch ($key) {
                            case 'abandoned_cart':
                                $dictionary[$key] = [
                                    'type'          => SegmentAbandonedCartQueryBuilder::getServiceId(),
                                    'foreign_table' => 'recommender_event_log',
                                    'field'         => $key,
                                ];
                            break;
                            case 'total_purchased_price':
                                $dictionary[$key] = [
                                    'type'          => SegmentAbandonedCartQueryBuilder::getServiceId(),
                                    'foreign_table' => 'recommender_event_log',
                                    'field'         => $key,
                                ];
                                break;
                        }
                        break;
                    case 'recommender_item':
                        $dictionary[$key] = [
                            'type'          => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\ItemQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'field'         => $key,
                        ];
                        break;
                    case 'recommender_item_property_value':
                        $dictionary[$key] = [
                            'type'          => ItemValueQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'field'         => $this->fields->cleanKey($key),
                        ];
                        break;
                    case 'recommender_event_log':
                    /*    $dictionary[$key] = [
                            'type'          => SegmentEventQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'foreign_table_field' => 'event_log_id',
                            'table_field'         => 'event_log_id',
                            'field'       => $key,
                        ];*/

                        $value = $key;
                        if (false !== strpos($key, 'date_added_')) {
                            $value            = str_replace('date_added_', '', $key);
                            $dictionary[$key] = [
                                'type'                => SegmentEventDateQueryBuilder::getServiceId(),
                                'foreign_table'       => $table,
                                'foreign_table_field' => $value,
                                'field'               => $value == $key ? $key : $value,
                            ];
                        } else {
                            $dictionary[$key] = [
                                'type'                => SegmentEventQueryBuilder::getServiceId(),
                                'foreign_table'       => $table,
                                'foreign_table_field' => $value,
                                'field'               => $value == $key ? $key : 'date_added',
                            ];
                        }
                        break;
                    case 'recommender_event_log_property_value':
                        $dictionary[$key] = [
                            'type'                => SegmentEventValueQueryBuilder::getServiceId(),
                            'foreign_table'       => $table,
                            'foreign_table_field' => 'event_log_id',
                            'table_field'         => 'event_log_id',
                            'field'               => $this->fields->cleanKey($key),
                        ];
                        break;
                }
            }
        }

        return $dictionary;
    }
}
