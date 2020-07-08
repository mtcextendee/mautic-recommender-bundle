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
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\FilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventDateQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventValueQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemValueQueryBuilder;
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
        $this->translator             = $translator;
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
            $events                                              = $this->recommenderClientModel->getEventRepository()->getEventNamesAsChoices();
            $this->fields['recommender_event_log']['event_id']   =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.name',
                    'properties' => [
                        'type' => 'select',
                        'list' => $events,
                    ],
                    'decorator' => [
                            'recommender' => ['type'=>ItemEventQueryBuilder::getServiceId()],
                        ],
                ];
            $this->fields['recommender_event_log']['date_added'] =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.date_added',
                    'properties' => [
                        'type' => 'datetime',
                    ],
                    'decorator' => [
                            'recommender' => ['type'=>ItemEventQueryBuilder::getServiceId()],
                        ],
                ];

            $this->fields['recommender_event_log']['weight'] =
                [
                    'name'       => 'mautic.plugin.recommender.form.event.weight',
                    'properties' => [
                        'type' => 'number',
                    ],
                    'decorator' => [
                            'recommender' => [
                                'type'                  => FilterQueryBuilder::getServiceId(),
                                'foreign_table'         => 'recommender_event',
                                'foreign_identificator' => 'id',
                            ],
                        ],
                ];

            foreach ($events as $eventId=>$eventName) {
                $this->fields['recommender_event_log']['date_added_'.$eventId] =
                    [
                        'name'       => strtoupper($eventName).' '.$this->translator->trans('mautic.plugin.recommender.form.event.date_added'),
                        'properties' => [
                            'type' => 'datetime',
                        ],
                        'decorator' => [
                                'key'         => $eventId,
                                'recommender' => [
                                    'type'=> ItemEventDateQueryBuilder::getServiceId(),

     'orderBy'=> 'IF(l.event_id = '.$eventId.', l.date_added, null)',
                                ],
                            ],
                    ];
            }
        } elseif ($table == 'recommender_item' && !isset($this->fields[$table])) {
            $this->fields['recommender_item']['item_id']        =
                    [
                        'name'       => 'mautic.plugin.recommender.form.item.id',
                        'properties' => [
                            'type' => 'text',
                        ],
                        'decorator' => [
                                'recommender' => ['type'=>ItemQueryBuilder::getServiceId()],
                            ],
                    ];
        } elseif ($table == 'recommender_event_log_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getEventLogValueRepository()->getValueProperties();
            foreach ($eventProperties as $property) {
                $property['decorator'] =
                    [
                        'key'         => $property['id'],
                        'recommender' => [
                            'type'   => ItemEventValueQueryBuilder::getServiceId(),
                            'orderBy'=> '(SELECT v.value
FROM recommender_event_log_property_value v WHERE v.event_log_id = l.id and v.property_id = '.$property['id'].')',
                        ],
                    ];
                $this->fields['recommender_event_log_property_value']['event_'.$property['id']] = $property;
            }
        } elseif ($table == 'recommender_item_property_value' && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getItemPropertyValueRepository()->getItemValueProperties();
            foreach ($eventProperties as $property) {
                $property['decorator'] =
                    [
                        'key'         => $property['id'],
                        'recommender' => [
                            'type'=> ItemValueQueryBuilder::getServiceId(),
                        ],
                    ];
                $this->fields['recommender_item_property_value']['item_'.$property['id']] = $property;
            }
        }

        return isset($this->fields[$table]) ? $this->fields[$table] : [];
    }

    /**
     * Remove prefix from property key (item_64, event_44).
     *
     * @param $key
     *
     * @return mixed
     */
    public function cleanKey($key)
    {
        return str_replace(['item_', 'event_'], '', $key);
    }
}
