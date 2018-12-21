<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Fields;

use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;

class Fields
{
    /** @var array */
    private $fields = [];

    /**
     * @var RecommenderClientModel
     */
    private $recommenderClientModel;

    public function __construct(RecommenderClientModel $recommenderClientModel)
    {
        $this->recommenderClientModel = $recommenderClientModel;

    }

    /**
     * @return string
     */
    public function getFields($table)
    {
        return $this->loadFields($table);
    }

    /**
     * @param $table
     *
     * @return array
     */
    private function loadFields($table)
    {
        // Load fields from recommender_event_log db table
        if ($table == 'recommender_event_log' && !isset($this->fields[$table])) {
            $this->fields['recommender_event_log']['event_id']   =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.name',
                    'properties' => [
                        'type' => 'multiselect',
                        'list' => $this->recommenderClientModel->getEventRepository()->getEventNamesAsChoices(),
                    ],
                ];
            $this->fields['recommender_event_log']['date_added'] =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.date_added',
                    'properties' => [
                        'type' => 'datetime',
                    ],
                ];
        }else if ($table == 'recommender_item' && !isset($this->fields[$table])) {
                $this->fields['recommender_item']['itemId']        =
                    [
                        'name'       => 'mautic.plugin.recommender.form.item.id',
                        'properties' => [
                            'type' => 'multiselect',
                            'list' => $this->recommenderClientModel->getRepository()->getEventNamesAsChoices(),
                        ],
                    ];
                $this->fields['recommender_event_log']['date_added'] =
                    [
                        'name'       => 'mautic.plugin.recommender.form.event.date_added',
                        'properties' => [
                            'type' => 'datetime',
                        ],
                    ];
            }
        elseif ($table == 'recommender_event_log_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getEventLogValueRepository()->getValueProperties();
            foreach ($eventProperties as $property) {
                $this->fields['recommender_event_log_property_value'][$property['id']] = $property;
            }
        }elseif ($table == 'recommender_item_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getItemPropertyValueRepository()->getItemValueProperties();
            foreach ($eventProperties as $property) {
                $this->fields['recommender_item_property_value'][$property['id']] = $property;
            }
        }
        return $this->fields[$table];
    }
}
