<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use Mautic\CoreBundle\Form\DataTransformer\ArrayStringTransformer;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use MauticPlugin\MauticRecommenderBundle\Enum\FiltersEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RecommenderPropertiesType.
 */
class RecommenderPropertiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'includeDisabledItems',
            YesNoButtonGroupType::class,
            [
                'label' => 'recommender.form.include.disabled.items',
                'attr'  => [
                ],
                'data' => isset($options['data']['includeDisabledItems']) ? (bool) $options['data']['includeDisabledItems'] : false,
            ]
        );

        $builder->add(
            $builder->create(
                'items',
                ItemListType::class,
                [
                    'label'      => 'recommender.form.items_limitation',
                    'label_attr' => ['class' => 'control-label'],
                    'multiple'   => true,
                    'required'   => false,
                    'attr'       => [
                    ],
                    'data' => array_map('strval', isset($options['data']['items']) ? $options['data']['items'] : []),
                ]
            )
        );

        $builder->add(
            $builder->create(
                'categories',
                ItemCategoriesListType::class,
                [
                    'label'      => 'recommender.form.categories_limitation',
                    'label_attr' => ['class' => 'control-label'],
                    'multiple'   => true,
                    'required'   => false,
                    'attr'       => [
                    ],
                    'data' => array_map('strval', isset($options['data']['categories']) ? $options['data']['categories'] : []),
                ]
            )
        );
    }
}
