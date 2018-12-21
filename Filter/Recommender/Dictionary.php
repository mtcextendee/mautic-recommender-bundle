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


use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Model\ListModel;
use Mautic\LeadBundle\Segment\Query\Filter\ComplexRelationValueFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\EventPropertyFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\ItemFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\ItemPropertyFilterQueryBuilder;
use Symfony\Component\Translation\TranslatorInterface;

class Dictionary
{
    CONST ALLOWED_TABLES = ['recommender_item','recommender_item_property_value', 'recommender_event_log'];

    /**
     * @var Fields
     */
    private $fields;


    /**
     * SegmentChoices constructor.
     *
     * @param Fields              $fields
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
                if (isset($field['type'])) {
                    $dictionary[$key] = [
                        'type'          => ItemFilterQueryBuilder::getServiceId(),
                        'foreign_table' => $table,
                        'field'         => $key,
                        ];
                }else{
                    $dictionary[$key] = [
                        'type'          => ItemPropertyFilterQueryBuilder::getServiceId(),
                        'foreign_table' => $table,
                        'foreign_table_field' => $key,
                        'field'       => 'value',
                    ];
                }
            }
        }

        die(print_r($dictionary));
        return $dictionary;
    }
}
