<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DashboardRecommenderAnalyticsWidgetType.
 */
class DashboardRecommenderAnalyticsWidgetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'source',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_source',

            ]
        );

        $builder->add(
            'source_dynamic',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_source',

            ]
        );

        $builder->add(
            'source_dynamic',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_source',

            ]
        );

        $builder->add(
            'medium',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_medium',
            ]
        );

        $builder->add(
            'campaign',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_name',
            ]
        );

        $builder->add(
            'adcontent',
            'yesno_button_group',
            [
                'label' => 'mautic.email.campaign_content',
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dashboard_extendee_analytics';
    }
}
