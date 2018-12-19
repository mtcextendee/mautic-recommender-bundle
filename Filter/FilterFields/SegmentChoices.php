<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\FilterFields;


use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Model\ListModel;

class SegmentChoices
{
    CONST ALLOWED_TABLES = ['recommender_event_log', 'recommender_event_log_property_value'];

    /**
     * @var Fields
     */
    private $fields;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * SegmentChoices constructor.
     *
     * @param Fields    $fields
     * @param ListModel $listModel
     */
    public function __construct(Fields $fields, ListModel $listModel)
    {

        $this->fields = $fields;
        $this->listModel = $listModel;
    }

    public function addChoices(LeadListFiltersChoicesEvent $event)
    {
        $choices = $this->getChoices();
        foreach ($choices as $key=>$options) {
            $event->addChoice('event', $key, $options);
        }
    }

    private function getChoices()
    {
        $choices = [];
        foreach (self::ALLOWED_TABLES as $table) {
            $fields = $this->fields->getFields($table);
            foreach ($fields as $key => $field) {
                $properties = [];
                if (isset($field['properties'])) {
                    $properties = $field['properties'];
                } elseif (isset($field['type'])) {
                    $properties['type'] = $field['type'];
                }
                $choices[$key] = [
                    'label'      => $field['name'],
                    'properties' => $properties,
                    'operators'  => $this->listModel->getOperatorsForFieldType(
                        $properties['type']
                    ),
                ];
            }
        }
        return $choices;
    }
}
