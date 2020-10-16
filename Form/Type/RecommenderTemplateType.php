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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RecommenderTemplateType.
 */
class RecommenderTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'header',
            TextareaType::class,
            [
                'label'    => 'mautic.plugin.recommender.template',
                'required' => false,
                'attr'     => [
                    'class' => 'recommender-template',
                    'rows'  => 3,
                ],
            ]
        );

        $builder->add(
            'body',
            TextareaType::class,
            [
                'label'       => 'mautic.plugin.recommender.template',
                'required'    => true,
                'attr'        => [
                    'class' => 'recommender-template',
                    'rows'  => 10,
                ],
            ]
        );

        $builder->add(
            'footer',
            TextareaType::class,
            [
                'label'    => 'mautic.plugin.recommender.template',
                'required' => false,
                'attr'     => [
                    'class' => 'recommender-template',
                    'rows'  => 3,
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'recommender_template';
    }
}
