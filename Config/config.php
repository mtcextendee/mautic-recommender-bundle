<?php

return [
    'name'        => 'Recommender',
    'description' => 'Recomendations engine',
    'author'      => 'kuzmany.biz',
    'version'     => '0.0.1',
    'services'    => [
        'events'       => [
            'mautic.recommender.js.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters'
                ],
            ],
            'mautic.recommender.pagebundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'mautic.recommender.helper',
                    'mautic.recommender.service.replacer',
                    'mautic.recommender.service.api.commands',
                    'mautic.recommender.service.token.html.replacer',
                    'mautic.campaign.model.event'
                ],
            ],
            'mautic.recommender.campaignbundle.subscriber'  => [
                    'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\CampaignSubscriber::class,
                    'arguments' => [
                        'mautic.lead.model.lead',
                        'mautic.email.model.email',
                        'mautic.campaign.model.event',
                        'mautic.email.model.send_email_to_user',
                        'mautic.recommender.service.replacer',
                        'mautic.recommender.service.campaign.lead.details',
                        'mautic.page.helper.tracking',
                        'mautic.focus.model.focus',
                        'session',
                        'mautic.helper.integration',
                        'mautic.dynamicContent.model.dynamicContent',
                        'doctrine.orm.entity_manager',
                        'mautic.notification.model.notification',
                        'mautic.notification.api',
                    ],
            ],
            'mautic.recommender.dynamic.content.token.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\DynamicContentTokenSubscriber::class,
                'arguments' => [
                    'mautic.recommender.service.replacer',
                    'doctrine.orm.entity_manager',
                    'mautic.recommender.service.campaign.lead.details'
                ],
            ],
            'mautic.recommender.leadbundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.recommender.helper',
                    'mautic.recommender.service.api.commands',
                ],
            ],
            'mautic.recommender.emailbundle.subscriber' => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.recommender.helper',
                    'mautic.recommender.service.replacer',
                ],
            ],
            'mautic.recommender.focus.token.subscriber'     => [
                'class'     => MauticPlugin\MauticRecommenderBundle\EventListener\FocusTokenSubscriber::class,
                'arguments' => [
                    'session',
                     'mautic.campaign.model.event',
                     'mautic.focus.model.focus'
                ],
            ],
        ],
        'models'       => [
            'mautic.recommender.model.recommender' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel::class,
            ],
            'mautic.recommender.model.client' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel::class,
                'arguments' => ['mautic.tracker.contact']
            ],
            'mautic.recommender.model.item' => [
                'class' => MauticPlugin\MauticRecommenderBundle\Model\ItemModel::class,
            ],
            'mautic.recommender.model.event.log' => [
                'class' => \MauticPlugin\MauticRecommenderBundle\Model\EventLogModel::class,
            ],
        ],
        'forms'        => [
            'mautic.form.type.recommender'         => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderType::class,
                'alias'     => 'recommender',
                'arguments' => [
                    'mautic.security',
                    'router',
                    'translator',
                ],
            ],
            'mautic.form.type.recommender.types'             => [
                'class' => MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTypesType::class,
                'alias' => 'recommender_types',
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
        ],
        'other'        => [
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
                    'mautic.recommender.model.recommender',
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
                    'mautic.campaign.model.campaign',
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
                    'mautic.page.model.trackable',
                ],
            ],
            'mautic.recommender.service.token.generator'     => [
                'class'     => MauticPlugin\MauticRecommenderBundle\Service\RecommenderGenerator::class,
                'arguments' => [
                    'mautic.recommender.model.recommender',
                    'mautic.recommender.api.recommender',
                    'mautic.lead.model.lead',
                    'twig',
                    'mautic.recommender.service.api.commands',
                    'mautic.helper.templating'
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
                'controller' => 'MauticRecommenderBundle:Recommender:template',
            ],
            'mautic_recommender_process_action' => [
                'path'       => '/recommender/send/event',
                'controller' => 'MauticRecommenderBundle:Recommender:process',
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
                    'route'    => 'mautic_recommender_index',
                    'access'   => ['recommender:recommender:viewown', 'recommender:recommender:viewother'],
                    'checks'   => [
                        'integration' => [
                            'Recommender' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'mautic.core.components',
                    'priority' => 100,
                ],
            ],
        ],
    ],
    'parameters'  => [
        'eventLabel'=> 'RecommenderEvent'
    ],
];
