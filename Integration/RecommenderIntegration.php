<?php

namespace MauticPlugin\MauticRecommenderBundle\Integration;

use Mautic\CoreBundle\Form\Type\SortableListType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\CoreBundle\Templating\Helper\AnalyticsHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Integration\EAnalyticsIntegration;
use MauticPlugin\MauticRecommenderBundle\Api\Client\Request\Item;
use MauticPlugin\MauticRecommenderBundle\Helper\GoogleAnalyticsHelper;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
                'allowedEvents',
                SortableListType::class,
                [
                    'label' => 'mautic.plugin.recommender.events',
                    'attr' => [
                        'tooltip' => 'mautic.plugin.recommender.events.tooltip'
                    ],
                    'option_required' => false,
                    'with_labels'     => true,
                    'required'        => false,
                    'key_value_pairs' => true,
                ]
            );



            $builder->add(
                'abandoned_cart',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.plugin.recommender.abandoned_cart_reminder',
                ]
            );


            $builder->add(
                'abandoned_cart_segment',
                'leadlist_choices',
                [
                    'label'      => 'mautic.recommender.segment.abandoned.cart',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.recommender.segment.abandoned.cart.tooltip',
                        'data-show-on' => '{"integration_details_featureSettings_abandoned_cart_1":["checked"]}',
                    ],
                    'multiple'   => false,
                    'expanded'   => false,
                ]
            );

            $builder->add(
                'abandoned_cart_order_segment',
                'leadlist_choices',
                [
                    'label'      => 'mautic.recommender.segment.abandoned.cart.order',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'tooltip'      => 'mautic.recommender.segment.abandoned.cart.order.tooltip',
                        'data-show-on' => '{"integration_details_featureSettings_abandoned_cart_1":["checked"]}',
                    ],
                    'multiple'   => false,
                    'expanded'   => false,
                ]
            );
        }
    }
}
