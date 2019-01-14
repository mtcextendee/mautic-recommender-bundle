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

use Mautic\LeadBundle\Entity\OperatorListTrait;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\Translation\TranslatorInterface;

class Fields
{
     use OperatorListTrait;

    /** @var array */
    private $fields = [];

    /**
     * @var RecommenderClientModel
     */
    private $recommenderClientModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Fields constructor.
     *
     * @param RecommenderClientModel $recommenderClientModel
     * @param TranslatorInterface    $translator
     */
    public function __construct(RecommenderClientModel $recommenderClientModel, TranslatorInterface $translator)
    {
        $this->recommenderClientModel = $recommenderClientModel;
        $this->translator = $translator;
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

            $events = $this->recommenderClientModel->getEventRepository()->getEventNamesAsChoices();
            $this->fields['recommender_event_log']['event_id']   =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.name',
                    'properties' => [
                        'type' => 'select',
                        'list' => $events,
                    ],
                ];
            $this->fields['recommender_event_log']['date_added'] =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.date_added',
                    'properties' => [
                        'type' => 'datetime',
                    ],
                    'operators'=> $this->getOperatorsForFieldType(
                        [
                            'include' => [
                                'in',
                                '!in',
                            ],
                        ]
                    )
                ];

            foreach ($events as $eventId=>$eventName) {
                $this->fields['recommender_event_log']['date_added_'.$eventId] =
                    [
                        'name'       => strtoupper($eventName).' '.$this->translator->trans('mautic.plugin.recommender.form.event.date_added'),
                        'properties' => [
                            'type' => 'datetime',
                        ],
                        'operators'=> $this->getOperatorsForFieldType(
                            [
                                'include' => [
                                    'in',
                                    '!in',
                                ],
                            ]
                        )
                    ];
            }

        }else if ($table == 'recommender_item' && !isset($this->fields[$table])) {
                $this->fields['recommender_item']['item_id']        =
                    [
                        'name'       => 'mautic.plugin.recommender.form.item.id',
                        'properties' => [
                            'type' => 'text'
                        ],
                    ];
            }
        elseif ($table == 'recommender_event_log_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getEventLogValueRepository()->getValueProperties();
            foreach ($eventProperties as $property) {
                $this->fields['recommender_event_log_property_value']['event_'.$property['id']] = $property;
            }
        }elseif ($table == 'recommender_item_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getItemPropertyValueRepository()->getItemValueProperties();
            foreach ($eventProperties as $property) {
                $this->fields['recommender_item_property_value']['item_'.$property['id']] = $property;
            }
        }
        return isset($this->fields[$table]) ? $this->fields[$table] : [];
    }

    /**
     * Remove prefix from property key (item_64, event_44)
     *
     * @param $key
     *
     * @return mixed
     */
    public function cleanKey($key)
    {
        return str_replace(['item_','event_'],'', $key);

    }
}
