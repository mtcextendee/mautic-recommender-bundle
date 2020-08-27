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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            $builder->create(
                'items',
                ItemListType::class,
                [
                    'label'      => false,
                    'label_attr' => ['class' => 'control-label'],
                    'multiple'   => true,
                    'required'   => false,
                    'attr'       => [
                        'data-show-on' => '{"recommender_filterTarget":"selected_items"}',
                    ],
                    'data' => array_map('strval', isset($options['data']['items']) ? $options['data']['items'] : []),
                ]
            )
        );
    }
}
