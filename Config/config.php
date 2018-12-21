<?php

return [
    'name'        => 'RecommenderTemplate',
    'description' => 'Recomendations engine',
    'author'      => 'kuzmany.biz',
    'version'     => '0.0.1',
    'services'    => [
        'events'       => [

            'mautic.recommender.recommender.filter.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Recommender\EventListener\FiltersSubscriber::class,
                'arguments' => [
                    'mautic.recommender.filter.segment.factory',
                ],
            ],

            'mautic.recommender.segment.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\FiltersSubscriber::class,
                'arguments' => [
                    'mautic.recommender.filter.segment.factory',
                    'mautic.recommender.filter.fields.segment'
                ],
            ],

            'mautic.recommender.filter.abandoned_cart'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\Filters\AbandonedCartFilterSubscriber::class,
                'arguments' => ['mautic.recommender.service.campaign.lead.details']
            ],
            'mautic.recommender.filter.points'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\Filters\PointsFilterSubscriber::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                    'mautic.recommender.filter.recommender'
                ]
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
            'mautic.form.type.recommender.focus.type' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderFocusType::class,
                'alias' => 'recommender_focus_type',
            ],
            'mautic.form.type.recommender.email.type' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderEmailSendType::class,
                'alias' => 'recommender_email_type',
            ],
            'mautic.form.type.recommender.dynamic_content.type' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentType::class,
                'alias' => 'recommender_dynamic_content_type',
            ],
            'mautic.form.type.recommender.dynamic_content.remove.type' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderDynamicContentRemoveType::class,
                'alias' => 'recommender_dynamic_content_remove_type',
            ],
            'mautic.form.type.recommender.options.type' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderOptionsType::class,
                'alias' => 'recommender_options_type',
                'arguments' => [
                    'event_dispatcher'
                ]
            ],
            'mautic.form.type.recommender.tags'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTagsType::class,
                'alias'     => 'recommender_tags',
                'arguments' => [
                    'mautic.recommender.service.api.commands',
                ],
            ],
            'mautic.form.type.recommender.utm_tags' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderUtmTagsType::class,
                'alias' => 'recommender_utm_tags',
            ],
            'mautic.form.type.recommender..notificationsend_list' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderNotificationSendType::class,
                'arguments' => 'router',
                'alias'     => 'recommender_notificationsend_list',
            ],
            'mautic.form.type.recommender.events_list' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\EventsListType::class,
                'arguments' => ['mautic.recommender.model.event'],
            ],
            'mautic.form.type.recommender.templates_list' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Form\Type\TemplatesListType::class,
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
            'mautic.recommender.filter.segment.factory'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\SegmentFilterFactory::class,
                'arguments' => [
                    '@service_container',
                    'mautic.lead.model.lead_segment_schema_cache',
                    'mautic.recommender.segment.decoration',
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
                    'mautic.recommender.model.client'
                ]
            ],
            'mautic.recommender.segment.decoration' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Decorator::class,
                'arguments' => [
                    'mautic.lead.model.lead_segment_filter_operator',
                    'mautic.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            'mautic.recommender.filter.fields.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Dictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ]
            ],
            'mautic.recommender.query.builder.event.property'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Query\EventPropertyFilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
            ],

            /* Filters  */
            'mautic.recommender.filter.recommender.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Dictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ]
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
                    'mautic.recommender.filter.segment.factory'
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
                    'event_dispatcher'
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
