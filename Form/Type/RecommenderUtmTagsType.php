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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RecommenderUtmTagsType.
 */
class RecommenderUtmTagsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'utmSource',
            'choice',
            [
                'choices'     => $options['utmSource'],
                'label'       => 'mautic.email.campaign_source',
                'label_attr'  => ['class' => 'control-label'],
                'multiple'    => true,
                'empty_value' => '',
                'attr'        => [
                    'class'   => 'form-control',
                ],
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
            'utmMedium',
            'choice',
            [
                'choices'     => $options['utmMedium'],
                'label'       => 'mautic.email.campaign_medium',
                'label_attr'  => ['class' => 'control-label'],
                'multiple'    => true,
                'empty_value' => '',
                'attr'        => [
                    'class'   => 'form-control',
                ],
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
            'utmCampaign',
            'choice',
            [
                'choices'     => $options['utmCampaign'],
                'label'       => 'mautic.email.campaign_name',
                'label_attr'  => ['class' => 'control-label'],
                'multiple'    => true,
                'empty_value' => '',
                'attr'        => [
                    'class'   => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    ),
                ],
            ]
        );
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(['utmCampaign','utmMedium','utmSource']);
    }

    /*
     * @return string
     */
    public function getName()
    {
        return 'recommender_utm_tags';
    }

}
