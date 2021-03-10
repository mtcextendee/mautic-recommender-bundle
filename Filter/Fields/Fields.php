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
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\AbandonedCartQueryBuilder;
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
     * Fields constructor.
     */
    public function __construct(RecommenderClientModel $recommenderClientModel, TranslatorInterface $translator)
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
        if ('recommenders' == $table && !isset($this->fields[$table])) {
            /*  $items = $this->recommenderClientModel->getItemPropertyValueRepository()->getValuesForProperty(
                  3 // @todo Add product name detection
              );
              $this->fields['recommenders']['items'] =
                  [
                      'name'       => 'recommender.form.selected_items',
                      'properties' => [
                          'type' => 'multiselect',
                          'list' => $items,
                      ],
                      'decorator'  => [
                          'recommender' => [
                              'type' => AbandonedCartQueryBuilder::getServiceId(),
                              'foreign_table'=> 'recommender_event_log_property_value'
                          ],
                      ],
                  ];*/

            /*$this->fields['recommenders']['best_selling'] =
                [
                    'name'       => 'recommender.form.best_selling',
                    'properties' => [
                        'type' => 'datetime',
                    ],
                    'operators'=> ['include'=> ['gt']],
                    'decorator'  => [
                        'recommender' => [
                            'type' => AbandonedCartQueryBuilder::getServiceId(),
                            'foreign_table'=> 'recommender_event_log_property_value'
                        ],
                    ],
                ];*/

            $this->fields['recommenders']['abandoned_cart'] =
                [
                    'name'       => 'recommender.form.event.abandoned_cart',
                    'properties' => [
                        'type' => 'datetime',
                    ],
                    'operators'=> ['include'=> ['gt']],
                ];

        /*  $this->fields['recommenders']['total_purchased_price'] =
              [
                  'name'       => 'recommender.form.event.total_purchased_price',
                  'properties' => [
                      'type' => 'number',
                  ],
              ];*/
        } elseif ('recommender_event_log' == $table && !isset($this->fields[$table])) {
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

        /*$this->fields['recommender_event_log']['weight'] =
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
            ];*/

            /*foreach ($events as $eventId=>$eventName) {
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
            }*/
        } elseif ('recommender_item' == $table && !isset($this->fields[$table])) {
            /* $this->fields['recommender_item']['item_id'] =
                 [
                     'name'       => 'mautic.plugin.recommender.form.item.id',
                     'properties' => [
                         'type' => 'text',
                     ],
                     'decorator'  => [
                         'recommender' => ['type' => ItemQueryBuilder::getServiceId()],
                     ],
                 ];*/
        } elseif ('recommender_event_log_property_value' == $table && !isset($this->fields[$table])) {
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
        } elseif ('recommender_item_property_value' == $table && !isset($this->fields[$table])) {
            $eventProperties = $this->recommenderClientModel->getItemPropertyValueRepository()->getItemValueProperties();
            foreach ($eventProperties as $property) {
                $property['decorator']                                                    =
                    [
                        'key'         => $property['id'],
                        'recommender' => [
                            'type' => ItemValueQueryBuilder::getServiceId(),
                        ],
                    ];
                /* if (4 == $property['id']) {
                     $categories = $this->recommenderClientModel->getItemPropertyValueRepository()->getValuesForProperty(
                         4 // @todo Add product name detection
                     );
                     if ($categories) {
                         $property['properties'] = [
                             'type' => 'multiselect',
                             'list' => $categories,
                         ];
                         $property['operators'] = [
                            'include'=> [
                                '=',
                                'empty',
                                'notEmpty',
                            ],
                        ];
                     }
                 }*/
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
