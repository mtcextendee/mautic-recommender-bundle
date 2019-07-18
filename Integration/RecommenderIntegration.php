<?php

namespace MauticPlugin\MauticRecommenderBundle\Integration;

use Mautic\CampaignBundle\Form\Type\CampaignListType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticRecommenderBundle\Form\Type\ListTemplateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RecommenderIntegration extends AbstractIntegration
{

    /**
     * RecommenderIntegration constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'Recommender';
    }

    public function getIcon()
    {
        return 'plugins/MauticRecommenderBundle/Assets/img/recommender.png';
    }

    public function getSupportedFeatures()
    {
        return [
        ];
    }

    public function getSupportedFeatureTooltips()
    {
        return [
            //    'tracking_page_enabled' => 'mautic.integration.form.features.tracking_page_enabled.tooltip',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        return [
        ];
    }

    /**
     * @return array
     */
    public function getFormSettings()
    {
        return [
            'requires_callback'      => false,
            'requires_authorization' => false,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'features') {
            $builder->add(
                'currency',
                TextType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.currency',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                    ],
                ]
            );

            $builder->add(
                'show_recommender_testbench',
                'yesno_button_group',
                [
                    'label' => 'mautic.plugin.recommender.form.testbench',
                    'attr'  => [
                        'tooltip' => 'mautic.plugin.recommender.form.testbench.tooltip',
                    ]                    
                ]
            );
            $builder->add(
                'items_import_url',
                UrlType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.items_import_url',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.plugin.recommender.form.items_import_url.tooltip',
                    ],
                ]
            );

            $builder->add(
                'events_import_url',
                UrlType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.events_import_url',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.plugin.recommender.form.events_import_url.tooltip',
                    ],
                ]
            );

            $builder->add(
                'batch_limit',
                NumberType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.batch_limit',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.plugin.recommender.form.batch_limit.tooltip',
                        'placeholder'  => '100',
                    ],
                ]
            );

            $builder->add(
                'timeout',
                TextType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.timeout',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.plugin.recommender.form.timeout.tooltip',
                        'placeholder'  => '-1 day',
                    ],
                ]
            );

        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $section
     *
     * @return string
     */
    public function getFormNotes($section)
    {
        if ('features' === $section) {
           return ['mautic.plugin.recommender.features.notes', 'warning'];
        }

        return parent::getFormNotes($section);        
    }
}
