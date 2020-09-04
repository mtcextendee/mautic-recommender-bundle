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

use Mautic\CoreBundle\Form\Type\EntityLookupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ItemListType.
 */
class ItemListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label'               => 'recommender.form.items',
                'model'               => 'recommender.client',
                'model_lookup_method' => 'getLookupResults',
                'ajax_lookup_action'  => 'plugin:recommender:getLookupChoiceList',
                'lookup_arguments'    => function (Options $options) {
                    return [
                        'type'   => 'recommender',
                        'filter' => '$data',
                        'limit'  => 100,
                        'start'  => 0,
                        'type'   => 'recommender.client',
                    ];
                },
                'multiple'            => true,
                'main_entity'         => null,
            ]
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return EntityLookupType::class;
    }
}
