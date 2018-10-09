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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RecommenderOptionsType.
 */
class RecommenderOptionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'type',
            'choice',
            [
                'choices'     => [
                    'recommend_items_to_user' => 'mautic.plugin.recommender.form.type.recommend_items_to_user',
                    /*'recommend_items_to_item' => 'mautic.plugin.recommender.form.type.recommend_items_to_item',*/
                    'abandoned_cart'  => 'mautic.plugin.recommender.form.type.abandoned_cart',
                    'advanced'        => 'mautic.plugin.recommender.form.type.advanced',
                ],
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.plugin.recommender.form.recommendations.type',
                'label_attr'  => ['class' => ''],
                'empty_value' => false,
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    ),
                ],
            ]
        );

       /* $builder->add(
            'numberOfItems',
            NumberType::class,
            [
                'label'       => 'mautic.plugin.recommender.form.number_of_items',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.plugin.recommender.form.number_of_items.tooltip',
                ],
                'required'    => false,
                'constraints' => [
                    new Range(
                        [
                            'min' => 1,
                        ]
                    ),
                ],
            ]
        );*/

        $builder->add(
            'filter',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.form.type.filter',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                    'tooltip'=>'mautic.plugin.recommender.form.type.filter.tooltip',
                    'data-show-on' => '{"campaignevent_properties_type_type":["advanced"]}',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'booster',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.form.type.booster',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                    'tooltip'=>'mautic.plugin.recommender.form.type.booster.tooltip',
                    'data-show-on' => '{"campaignevent_properties_type_type":["advanced"]}',

                ],
                'required'   => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender_options_type';
    }
}
