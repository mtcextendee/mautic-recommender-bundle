<?php

return [
    'name'        => 'RecommenderTemplate',
    'description' => 'Recomendations engine',
    'author'      => 'kuzmany.biz',
    'version'     => '0.0.1',
    'services'    => [
        'events'       => [

            /* Recommender filters  */
            'mautic.recommender.filter.filters'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Recommender\EventListener\FiltersFilterSubscriber::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                    'mautic.recommender.filter.recommender',
                    'mautic.recommender.filter.factory'
                ]
            ],

            /* Segment filters  */
            'mautic.recommender.segment.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\FiltersSubscriber::class,
                'arguments' => [
                    'mautic.recommender.filter.factory',
                    'mautic.recommender.filter.fields.recommender',
                    'mautic.recommender.segment.decoration'
                ],
            ],

            'mautic.recommender.js.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters'
                ],
            ],
            'mautic.recommender.pagebundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'mautic.recommender.service.replacer',
                    'mautic.tracker.contact'
                ],
            ],
            'mautic.recommender.token.replacer.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\TokenReplacementSubscriber::class,
                'arguments' => [
                    'mautic.recommender.service.replacer',
                    'mautic.dynamicContent.model.dynamicContent',
                    'mautic.focus.model.focus',
                ],
            ],
            'mautic.recommender.emailbundle.subscriber' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.recommender.helper',
                    'mautic.recommender.service.replacer',
                ],
            ],
        ],
        'models'       => [
            'mautic.recommender.model.recommender' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel::class,
            ],
            'mautic.recommender.model.template' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\TemplateModel::class,
            ],
            'mautic.recommender.model.event' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel::class,
            ],
            'mautic.recommender.model.client' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel::class,
                'arguments' => ['mautic.tracker.contact']
            ],
        ],
        'forms'        => [
            'mautic.form.type.recommender.table_order'         => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTableOrderType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'mautic.form.type.recommender.filters'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\FilterType::class,
                'alias'     => 'recommender_filters',
                'arguments' => [
                    'translator',
                ],
            ],
            'mautic.form.type.recommender.template'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTemplatesType::class,
                'alias'     => 'recommender_templates',
                'arguments' => [
                    'mautic.security',
                    'router',
                    'translator',
                ],
            ],
            'mautic.form.type.recommender.event'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEventType::class,
                'alias'     => 'recommender_event',
                'arguments' => [
                    'mautic.security',
                    'router',
                    'translator',
                ],
            ],
            'mautic.form.type.recommender.recommender_template' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTemplateType::class,
                'alias' => 'recommender_template',
            ],
            'mautic.form.type.recommender.recommender_properties' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderPropertiesType::class,
                'alias' => 'recommender_properties',
            ],
            'mautic.form.type.recommender.tags'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTagsType::class,
                'alias'     => 'recommender_tags',
                'arguments' => [
                    'mautic.recommender.service.api.commands',
                ],
            ],
            'mautic.form.type.recommender.events_list' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\ListEventsType::class,
                'arguments' => ['mautic.recommender.model.event'],
            ],
            'mautic.form.type.recommender.templates_list' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\ListTemplatesType::class,
                'arguments' => ['mautic.recommender.model.template'],
            ],
            'mautic.form.type.recommender' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderType::class,
                'arguments' => [
                    'event_dispatcher',
                    'doctrine.orm.entity_manager',
                    'translator',
                    'mautic.lead.model.list',
                    'mautic.recommender.model.client',
                    'mautic.recommender.filter.fields.recommender'
                ],
            ],
        ],
        'other'        => [
            /* Filters */
            'mautic.recommender.filter.factory'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory::class,
                'arguments' => [
                    '@service_container',
                    'mautic.lead.model.lead_segment_schema_cache',
                ]
            ],
            'mautic.recommender.filter.fields.segment'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\Choices::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                    'mautic.lead.model.list',
                    'translator'
                ]
            ],
            'mautic.recommender.filter.fields'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                    'translator'
                ]
            ],
            'mautic.recommender.segment.decoration' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Decorator::class,
                'arguments' => [
                    'mautic.lead.model.lead_segment_filter_operator',
                    'mautic.lead.repository.lead_segment_filter_descriptor',
                    'mautic.recommender.filter.fields.dictionary'
                ],
            ],
            'mautic.recommender.filter.fields.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\SegmentDictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ]
            ],
            'mautic.recommender.query.builder.segment.event'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.segment.event_date'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventDateQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.event.property'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Query\EventPropertyFilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],

            /* Recommender filters  */
            'mautic.recommender.recommender.decoration' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\Decorator::class,
                'arguments' => [
                    'mautic.lead.model.lead_segment_filter_operator',
                    'mautic.lead.repository.lead_segment_filter_descriptor',
                    'mautic.recommender.filter.recommender.dictionary'
                ],
            ],
            'mautic.recommender.filter.recommender.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\RecommenderDictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ]
            ],
            'mautic.recommender.query.builder.recommender.item'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.recommender.item_value'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemValueQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.recommender.event'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.recommender.event_value'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventValueQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.recommender.event.date_added'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventDateQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            /* segment filter dictionary */
            'mautic.recommender.query.builder.segment.item'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\ItemQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.segment.item_value'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\ItemValueQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],


            'mautic.recommender.query.builder.segment.event_value'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventValueQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.base.item'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Query\BaseFilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.item.property'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Query\ItemPropertyFilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.query.builder.item'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Query\ItemFilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],
            'mautic.recommender.filter.recommender'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\RecommenderQueryBuilder::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.lead.model.random_parameter_name',
                    'event_dispatcher',
                    'mautic.recommender.filter.factory',
                    'mautic.recommender.recommender.decoration'
                ]
            ],
            'mautic.recommender.filter.fields.recommender'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Choices::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                    'mautic.lead.model.list',
                    'translator'
                ]
            ],

            /* Client */
            'mautic.recommender.client'=> [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Api\Client\Client::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                ],
            ],

            'mautic.recommender.helper'                      => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.recommender.model.template',
                    'translator',
                    'mautic.security',
                    'doctrine.orm.entity_manager'
                ],
            ],
            'mautic.recommender.api.recommender'                => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi::class,
                'arguments' => [
                    'mautic.page.model.trackable',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                    'mautic.helper.template.version',
                    'mautic.recommender.client'
                ],
            ],
            'mautic.recommender.service.api.commands'        => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands::class,
                'arguments' => [
                    'mautic.recommender.api.recommender',
                    'monolog.logger.mautic',
                    'translator',
                    'mautic.recommender.service.api.segment.mapping',
                    'mautic.recommender.service.token.finder',
                    'event_dispatcher',
                    'doctrine.orm.entity_manager'
                ],
            ],
            'mautic.recommender.service.api.segment.mapping' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Api\Service\SegmentMapping::class,
                'arguments' => [
                    'mautic.lead.model.list',
                    'mautic.helper.integration',
                ],
            ],
            'mautic.recommender.service.token'               => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken::class,
                'arguments' => [
                    'mautic.recommender.model.recommender',
                    'mautic.lead.model.lead',
                ],
            ],
            'mautic.recommender.service.token.finder'        => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder::class,
                'arguments' => [
                    'mautic.recommender.service.token',
                ],
            ],
            'mautic.recommender.service.replacer'            => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer::class,
                'arguments' => [
                    'mautic.recommender.service.token',
                    'mautic.recommender.service.token.finder',
                    'mautic.recommender.service.token.generator',
                ],
            ],
            'mautic.recommender.service.token.generator'     => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderGenerator::class,
                'arguments' => [
                    'mautic.recommender.model.template',
                    'mautic.recommender.api.recommender',
                    'mautic.lead.model.lead',
                    'twig',
                    'mautic.recommender.service.api.commands',
                    'mautic.helper.templating',
                    'event_dispatcher'
                ],
            ],
            'mautic.recommender.service.token.html.replacer' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenHTMLReplacer::class,
                'arguments' => [
                    'mautic.recommender.service.token.generator',
                    'mautic.recommender.service.token',
                ],
            ],
            'mautic.recommender.service.campaign.lead.details' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails::class,
                'arguments' => [
                    'mautic.campaign.model.campaign',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.recommender' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration::class,
                'arguments' => [
                ],
            ],
        ],
    ],
    'routes'      => [
        'main'   => [
            'mautic_recommender_template_index'  => [
                'path'       => '/recommenderTemplate/{page}',
                'controller' => 'MauticRecommenderBundle:RecommenderTemplate:index',
            ],
            'mautic_recommender_template_action' => [
                'path'       => '/recommenderTemplate/{objectAction}/{objectId}',
                'controller' => 'MauticRecommenderBundle:RecommenderTemplate:execute',
            ],
            'mautic_recommender_event_index'  => [
                'path'       => '/recommenderEvent/{page}',
                'controller' => 'MauticRecommenderBundle:RecommenderEvent:index',
            ],
            'mautic_recommender_event_action' => [
                'path'       => '/recommenderEvent/{objectAction}/{objectId}',
                'controller' => 'MauticRecommenderBundle:RecommenderEvent:execute',
            ],
            'mautic_recommender_index'  => [
                'path'       => '/recommender/{page}',
                'controller' => 'MauticRecommenderBundle:Recommender:index',
            ],
            'mautic_recommender_action' => [
                'path'       => '/recommender/{objectAction}/{objectId}',
                'controller' => 'MauticRecommenderBundle:Recommender:execute',
            ],
        ],
        'public' => [
            'mautic_recommender_generate_template' => [
                'path'       => '/recommender/template/{id}',
                'controller' => 'MauticRecommenderBundle:RecommenderTemplate:template',
            ],
            'mautic_recommender_send_event' => [
                'path'       => '/recommender/event/send',
                'controller' => 'MauticRecommenderBundle:Recommender:send',
            ],
        ],
        'api'    => [
            'mautic_recommender_api' => [
                'path'       => '/recommender/{component}',
                'controller' => 'MauticRecommenderBundle:Api\RecommenderApi:process',
                'method'     => 'POST',
            ],
        ],
    ],
    'menu'        => [
        'main' => [
            'items' => [
                'mautic.plugin.recommender' => [
                    'access'   => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
                    'iconClass' => 'fa fa-table',
                    'checks'   => [
                        'integration' => [
                            'Recommender' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'priority' => 70,
                ],
                'mautic.plugin.recommender.event' => [
                    'route'    => 'mautic_recommender_event_index',
                    'access'   => ['recommender:recommenderEvent:viewown', 'recommender:recommenderEvent:viewother'],
                    'checks'   => [
                        'integration' => [
                            'Recommender' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'mautic.plugin.recommender',
                    'priority' => 100,
                ],
                'mautic.plugin.recommender.templates' => [
                    'route'    => 'mautic_recommender_template_index',
                    'access'   => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
                    'checks'   => [
                        'integration' => [
                            'Recommender' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'mautic.plugin.recommender',
                    'priority' => 50,
                ],

                'mautic.plugin.recommenders' => [
                    'route'    => 'mautic_recommender_index',
                    'access'   => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
                    'checks'   => [
                        'integration' => [
                            'Recommender' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'mautic.plugin.recommender',
                    'priority' => 30,
                ],
            ],
        ],
    ],
    'parameters'  => [
        'eventLabel'=> 'RecommenderEvent'
    ],
];
