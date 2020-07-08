<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RecommenderPropertiesType.
 */
class RecommenderPropertiesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'columns',
            ChoiceType::class,
            [
                'choices' => [
                    '2'  => '6',
                    '3'  => '4',
                    '4'  => '3',
                    '6'  => '2',
                    '12' => '1',
                ],
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.recommender.form.columns',
                'label_attr'  => ['class' => ''],
                'empty_value' => false,
                'required'    => true,
                'data'        => isset($options['data']['columns']) ? $options['data']['columns'] : 3,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'background',
            'text',
            [
                'label'      => 'mautic.recommender.form.background.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'font',
            'choice',
            [
                'choices' => [
                    'Arial, Helvetica, sans-serif'                             => 'Arial',
                    '\'Arial Black\', Gadget, sans-serif'                      => 'Arial Black',
                    '\'Arial Narrow\', sans-serif'                             => 'Arial Narrow',
                    'Century Gothic, sans-serif'                               => 'Century Gothic',
                    'Copperplate / Copperplate Gothic Light, sans-serif'       => 'Copperplate Gothic Light',
                    '\'Courier New\', Courier, monospace'                      => 'Courier New',
                    'Georgia, Serif'                                           => 'Georgia',
                    'Impact, Charcoal, sans-serif'                             => 'Impact',
                    '\'Lucida Console\', Monaco, monospace'                    => 'Lucida Console',
                    '\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif'   => 'Lucida Sans Unicode',
                    '\'Palatino Linotype\', \'Book Antiqua\', Palatino, serif' => 'Palatino',
                    'Tahoma, Geneva, sans-serif'                               => 'Tahoma',
                    '\'Times New Roman\', Times, serif'                        => 'Times New Roman',
                    '\'Trebuchet MS\', Helvetica, sans-serif'                  => 'Trebuchet MS',
                    'Verdana, Geneva, sans-serif'                              => 'Verdana',
                ],
                'label'      => 'mautic.focus.form.font',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                ],
                'required'    => false,
                'empty_value' => false,
            ]
        );

        $builder->add(
            'padding',
            'text',
            [
                'label'      => 'mautic.recommender.form.padding',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'style',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'colBackground',
            'text',
            [
                'label'      => 'mautic.recommender.form.background.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'colPadding',
            'text',
            [
                'label'      => 'mautic.recommender.form.padding',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'colStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemName',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemNameColor',
            'text',
            [
                'label'      => 'mautic.recommender.form.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemNameSize',
            'text',
            [
                'label'      => 'mautic.recommender.form.font.size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemNamePadding',
            'text',
            [
                'label'      => 'mautic.recommender.form.padding',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemNameBold',
            'yesno_button_group',
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemNameBold']) ?: false,
            ]
        );

        $builder->add(
            'itemNameStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemImage',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.image',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemImageStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemShortDescription',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.short.description',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemShortDescriptionBold',
            'yesno_button_group',
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemShortDescriptionBold']) ?: false,
            ]
        );

        $builder->add(
            'itemShortDescriptionStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemUrl',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.url',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemAction',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.item.action',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionBackground',
            'text',
            [
                'label'      => 'mautic.recommender.form.background.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionHover',
            'text',
            [
                'label'      => 'mautic.recommender.form.background.hover.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionColor',
            'text',
            [
                'label'      => 'mautic.recommender.form.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionPadding',
            'text',
            [
                'label'      => 'mautic.recommender.form.padding',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionRadius',
            'text',
            [
                'label'      => 'mautic.recommender.form.radius',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionSize',
            'text',
            [
                'label'      => 'mautic.recommender.form.font.size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemActionBold',
            'yesno_button_group',
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemActionBold']) ?: false,
            ]
        );

        $builder->add(
            'itemActionStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemPrice',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.price',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemPriceColor',
            'text',
            [
                'label'      => 'mautic.recommender.form.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemPricePadding',
            'text',
            [
                'label'      => 'mautic.recommender.form.padding',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemPriceSize',
            'text',
            [
                'label'      => 'mautic.recommender.form.font.size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemPriceBold',
            'yesno_button_group',
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemPriceBold']) ?: false,
            ]
        );

        $builder->add(
            'itemPriceStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemOldPrice',
            RecommenderTagsType::class,
            [
                'label'      => 'mautic.plugin.recommender.item.old.price',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemOldPriceColor',
            'text',
            [
                'label'      => 'mautic.recommender.form.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemOldPriceSize',
            'text',
            [
                'label'      => 'mautic.recommender.form.font.size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemOldPriceStyle',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.style',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'itemOldPriceBold',
            'yesno_button_group',
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemOldPriceBold']) ?: false,
            ]
        );

        $builder->add(
            'header',
            'textarea',
            [
                'label'      => 'mautic.plugin.recommender.header',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control editor editor-basic',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'footer',
            'textarea',
            [
                'label'      => 'mautic.plugin.recommender.footer',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control editor editor-basic',
                ],
                'required' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender_properties';
    }
}
