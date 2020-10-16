<?php

namespace MauticPlugin\MauticRecommenderBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticRecommenderBundle\Form\Type\ListTemplateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class RecommenderIntegration extends AbstractIntegration
{
    const NAME           = 'Recommender';
    const DISPLAY_NAME   = 'Recomendations';
    const IMPORT_TIMEOUT = '-1 day';
    const IMPORT_BATCH   = 100;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    public function getDisplayName()
    {
        return self::DISPLAY_NAME;
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
        if ('features' == $formArea) {
            $builder->add(
                'currency',
                TextType::class,
                [
                    'label'      => 'mautic.plugin.recommender.form.currency',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                    ],
                    'required' => false,
                ]
            );

            $builder->add(
                'show_recommender_testbench',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.plugin.recommender.form.testbench',
                    'attr'  => [
                        'tooltip' => 'mautic.plugin.recommender.form.testbench.tooltip',
                    ],
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
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
                        'placeholder'  => self::IMPORT_BATCH,
                    ],
                    'required' => false,
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
                        'placeholder'  => self::IMPORT_TIMEOUT,
                    ],
                    'required' => false,
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
