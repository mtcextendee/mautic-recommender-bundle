<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\SortableListType;
use MauticPlugin\MauticFocusBundle\Form\Type\FocusShowType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RecommenderFocusShowType.
 */
class RecommenderFocusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'focus',
            FocusShowType::class,
            [
                'label' => false,
                'data' => isset($options['data']['focus']) ? $options['data']['focus']: null,

            ]
        );

        if (!empty($options['urls'])) {
            $builder->add(
                'includeUrls',
                SortableListType::class,
                [
                    'label' => 'mautic.page.include.urls',
                    'attr' => [
                      'tooltip' => 'mautic.page.urls.desc'
                    ],
                    'option_required' => false,
                    'with_labels'     => false,
                    'required'        => false,
                ]
            );

            $builder->add(
                'excludeUrls',
                SortableListType::class,
                [
                    'label'           => 'mautic.page.include.urls',
                    'attr' => [
                        'tooltip' => 'mautic.page.urls.desc'
                    ],
                    'option_required' => false,
                    'with_labels'     => false,
                    'required'        => false,
                ]
            );
        }

        $builder->add(
            'type',
            RecommenderOptionsType::class,
            [
                'label' => false,
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['update_select', 'urls']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender_focus_type';
    }
}
