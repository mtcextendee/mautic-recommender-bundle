<?php

use Doctrine\ORM\EntityRepository;

return [
    'name'        => \MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration::DISPLAY_NAME,
    'description' => 'Recomendations engine',
    'author'      => 'webmecanik.com',
    'version'     => '1.0.0',
    'services'    => [
        'events'       => [
            /* Recommender filters  */
            'mautic.recommender.filter.filters'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Recommender\EventListener\FiltersFilterSubscriber::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                    'mautic.recommender.filter.recommender',
                    'mautic.recommender.filter.factory',
                ],
            ],

            /* Segment filters  */
            'mautic.recommender.segment.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\FiltersSubscriber::class,
                'arguments' => [
                    'mautic.recommender.filter.factory',
                    'mautic.recommender.filter.fields.recommender',
                    'mautic.recommender.segment.decoration',
                    'mautic.helper.integration',
                    'request_stack',
                ],
            ],

            'mautic.recommender.js.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.helper.integration',
                    'router',
                ],
            ],
            'mautic.recommender.pagebundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'mautic.recommender.service.replacer',
                    'mautic.tracker.contact',
                    'mautic.helper.integration',
                    'mautic.helper.token_builder.factory',
                ],
            ],
            'mautic.recommender.token.replacer.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\TokenReplacementSubscriber::class,
                'arguments' => [
                    'mautic.recommender.service.replacer',
                    'mautic.dynamicContent.model.dynamicContent',
                    'mautic.focus.model.focus',
                    'mautic.helper.integration',
                    'mautic.tracker.contact',
                ],
            ],
            'mautic.recommender.lead.timeline.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'translator',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.recommender.emailbundle.subscriber' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.recommender.helper',
                    'mautic.recommender.service.replacer',
                    'mautic.helper.integration',
                    'mautic.email.model.email',
                    'mautic.helper.token_builder.factory',
                ],
            ],
            'mautic.recommender.maintenance.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\MaintenanceSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                ],
            ],

            'mautic.recommender.query.selected_items.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQuerySelectedItemsSubscriber::class,
            ],
            'mautic.recommender.query.selected_categories.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQuerySelectedCategoriesSubscriber::class,
                'arguments' => [
                    'mautic.recommender.properties'
                ]
            ],
            'mautic.recommender.query.best_sellers.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQueryBestSellersSubscriber::class,
                'arguments' => [
                    'mautic.recommender.properties'
                ]
            ],
            'mautic.recommender.query.popular_products.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQueryPopularProductsSubscriber::class,
            ],
            'mautic.recommender.query.abandoned_cart.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQueryAbandonedCartSubscriber::class,
                'arguments' => [
                    'mautic.recommender.properties'
                ]
            ],
            'mautic.recommender.query.recently_created.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQueryRecentlyCreatedSubscriber::class,
            ],
            'mautic.recommender.query.context.subscriber' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\EventListener\RecommenderQueryContextSubscriber::class,
                'arguments' => [
                    'mautic.recommender.filter.token.context',
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
                'class'     => MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel::class,
                'arguments' => ['mautic.tracker.contact'],
            ],
            'mautic.recommender.model.property' => [
                'class' => \MauticPlugin\MauticRecommenderBundle\Model\RecommenderPropertyModel::class,
            ],
            'mautic.recommender.model.categories' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Model\RecommenderCategoriesModel::class,
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
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTemplatesPropertiesType::class,
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
                    'mautic.recommender.filter.fields.recommender',
                    'router',
                ],
            ],
        ],
        'other'        => [
            'mautic.recommender.filter.token.context' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Token\ContextToken::class,
                'arguments' => [
                    'mautic.lead.model.random_parameter_name',
                    'mautic.recommender.model.property',
                    'mautic.recommender.model.event',
                ],
            ],

            'mautic.recommender.logger' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Logger\DebugLogger::class,
                'arguments' => [
                    'monolog.logger.mautic',
                ],
            ],
            'mautic.recommender.integration.settings'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Integration\RecommenderSettings::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.helper.core_parameters',
                    'mautic.recommender.model.event',
                    'mautic.recommender.model.property',
                ],
                'methodCalls' => [
                    'initiateDebugLogger' => ['mautic.recommender.logger'],
                ],
            ],

            'mautic.recommender.properties'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Integration\RecommenderProperties::class,
                'arguments' => [
                    'mautic.recommender.model.property',
                    'mautic.recommender.model.event',
                ],
            ],

            'mautic.recommender.contact.search'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Service\ContactSearch::class,
                'arguments' => [
                    '@service_container',
                ],
            ],

            'mautic.recommender.events.processor'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Events\Processor::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.security',
                    'mautic.recommender.service.api.commands',
                    'mautic.recommender.model.event',
                    'translator',
                    'mautic.lead.model.lead',
                    'mautic.tracker.contact',
                ],
            ],

            /* Filters */
            'mautic.recommender.filter.factory'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory::class,
                'arguments' => [
                    '@service_container',
                    'mautic.lead.model.lead_segment_schema_cache',
                ],
            ],
            'mautic.recommender.filter.fields'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields::class,
                'arguments' => [
                    'mautic.recommender.model.client',
                    'translator',
                ],
            ],
            'mautic.recommender.segment.decoration' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Decorator::class,
                'arguments' => [
                    'mautic.lead.model.lead_segment_filter_operator',
                    'mautic.lead.repository.lead_segment_filter_descriptor',
                    'mautic.recommender.filter.fields.dictionary',
                ],
            ],
            'mautic.recommender.filter.fields.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\SegmentDictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ],
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
                    'mautic.recommender.filter.recommender.dictionary',
                ],
            ],
            'mautic.recommender.filter.recommender.dictionary'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\RecommenderDictionary::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                ],
            ],
            'mautic.recommender.filter.recommender.orderby'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator\RecommenderOrderBy::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                    'mautic.lead.model.random_parameter_name',
                ],
            ],
            'mautic.recommender.query.builder.recommender.filter'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\FilterQueryBuilder::class,
                'arguments' => ['mautic.lead.model.random_parameter_name'],
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
            'mautic.recommender.query.builder.recommender.abandoned_cart'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\AbandonedCartQueryBuilder::class,
                'arguments' => [
                    'mautic.lead.model.random_parameter_name',
                    'mautic.recommender.model.client',
                ],
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
            'mautic.recommender.query.builder.abandoned_cart'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentAbandonedCartQueryBuilder::class,
                'arguments' => [
                    'mautic.lead.model.random_parameter_name',
                    'mautic.recommender.model.client',
                ],
            ],
            'mautic.recommender.filter.recommender'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\RecommenderQueryBuilder::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.lead.model.random_parameter_name',
                    'mautic.recommender.filter.factory',
                    'mautic.recommender.recommender.decoration',
                    'mautic.recommender.filter.recommender.orderby',
                    'event_dispatcher',
                ],
            ],
            'mautic.recommender.filter.fields.recommender'  => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Choices::class,
                'arguments' => [
                    'mautic.recommender.filter.fields',
                    'mautic.lead.model.list',
                    'translator',
                ],
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
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.recommender.api.recommender'                => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi::class,
                'arguments' => [
                    'mautic.page.model.trackable',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                    'mautic.helper.template.version',
                    'mautic.recommender.client',
                ],
            ],
            'mautic.recommender.service.api.commands'        => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands::class,
                'arguments' => [
                    'mautic.recommender.api.recommender',
                    'monolog.logger.mautic',
                    'translator',
                    'mautic.recommender.service.token.finder',
                    'event_dispatcher',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.recommender.service.token'               => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken::class,
                'arguments' => [
                    'mautic.recommender.model.recommender',
                    'mautic.helper.integration',
                    'mautic.tracker.contact',
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
                    'event_dispatcher',
                ],
            ],
            'mautic.recommender.service.token.html.replacer' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenHTMLReplacer::class,
                'arguments' => [
                    'mautic.recommender.service.token.generator',
                    'mautic.recommender.service.token',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.recommender' => [
                'class'     => \MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
        'repositories' => [
            'mautic.recommender.repository.item' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \MauticPlugin\MauticRecommenderBundle\Entity\Item::class,
                ],
            ],
            'mautic.recommender.repository.property' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \MauticPlugin\MauticRecommenderBundle\Entity\Property::class,
                ],
            ],
            'mautic.recommender.repository.item.property.value' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue::class,
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
            'mautic_recommender_get_available_functions' => [
                'path'       => '/recommender/getfunctions',
                'controller' => 'MauticRecommenderBundle:Ajax:listavailablefunctions',
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
            'mautic_recommender_dwc' => [
                'path'       => '/recommender/dwc/{objectId}',
                'controller' => 'MauticRecommenderBundle:Ajax:dwc',
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
                    'access'    => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
                    'iconClass' => 'fa fa-table',
                    'checks'    => [
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
                    'access'   => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
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
    'categories' => [
        'plugin:recommender' => 'mautic.recommender',
    ],
    'parameters'  => [
        'eventLabel'                => 'RecommenderEvent',
        'recommender_ai'            => true,
        'recommender_ai_database'   => 'kuzmany-shopify',
        'recommender_ai_secret_key' => 'jt9KE1ZOzQCHXN61OjS7KOxKDasVTZxHYKaSIKfpLSsuDqeflJ7xFv1r5Oz8Q5e2',
    ],
];
