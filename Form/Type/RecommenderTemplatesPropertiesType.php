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

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RecommenderPropertiesType.
 */
class RecommenderTemplatesPropertiesType extends AbstractType
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
                'choices' => array_flip($this->getColumnsNumbers()),

                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.recommender.form.columns',
                'label_attr'  => ['class' => ''],
                'placeholder' => false,
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
            TextType::class,
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
            ChoiceType::class,
            [
                'choices' => array_flip($this->getFonts()),
                'label'      => 'mautic.focus.form.font',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                ],
                'required'    => false,
                'placeholder' => false,
            ]
        );

        $builder->add(
            'padding',
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data' => isset($options['data']['itemNameBold']) ? (bool) $options['data']['itemNameBold'] : false,
            ]
        );

        $builder->add(
            'itemNameStyle',
            TextType::class,
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
            TextType::class,
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
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemShortDescriptionBold']) ? (bool) $options['data']['itemShortDescriptionBold'] : false,
            ]
        );

        $builder->add(
            'itemShortDescriptionStyle',
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemActionBold']) ? (bool) $options['data']['itemActionBold'] : false,
            ]
        );

        $builder->add(
            'itemActionStyle',
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemPriceBold']) ? (bool) $options['data']['itemPriceBold'] : false,
            ]
        );

        $builder->add(
            'itemPriceStyle',
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            TextType::class,
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
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recommender.bold',
                'attr'  => [
                ],
                'data'        => isset($options['data']['itemOldPriceBold']) ? (bool) $options['data']['itemOldPriceBold'] : false,
            ]
        );

        $builder->add(
            'header',
            TextareaType::class,
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
            TextareaType::class,
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
    public function getBlockPrefix()
    {
        return 'recommender_properties';
    }

    private function getFonts()
    {
        return  [
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
        ];

    }

    private function getColumnsNumbers()
    {
        return [
            '2'  => '6',
            '3'  => '4',
            '4'  => '3',
            '6'  => '2',
            '12' => '1',
        ];
    }
}
