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

use Mautic\CoreBundle\Form\Type\ButtonGroupType;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Range;

class RecommenderTemplatesType extends AbstractType
{
    /**
     * @var \Mautic\CoreBundle\Security\Permissions\CorePermissions
     */
    protected $security;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * CompanyType constructor.
     */
    public function __construct(CorePermissions $security, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->security   = $security;
        $this->router     = $router;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label'      => 'mautic.core.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => true,
            ]
        );

        $builder->add(
            'numberOfItems',
            NumberType::class,
            [
                'label'       => 'mautic.plugin.recommender.form.number_of_items.default',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.plugin.recommender.form.number_of_items.tooltip',
                ],
                'required'    => false,
                'data'        => $options['data']->getNumberOfItems(),
                'constraints' => [
                    new Range(
                        [
                            'min' => 1,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'templateMode',
            ButtonGroupType::class,
            [
                'label'      => 'mautic.plugin.recommender.form.template_mode',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'mautic.plugin.recommender.form.template_mode.tooltip',
                ],
                'choices'           => $this->getTemplateModes(),
                'data'              => $options['data']->getTemplateMode() ?: 'basic',
            ]
        );

        $builder->add(
            'properties',
            RecommenderTemplatesPropertiesType::class,
            [
                'label'      => false,
                'attr'       => [
                    'data-show-on' => '{"recommender_templateMode_0":"checked"}',
                ],
                'data'=> $options['data']->getProperties(),
            ]
        );

        $builder->add(
            'template',
            RecommenderTemplateType::class,
            [
                'label'      => 'mautic.plugin.recommender.template',
                'attr'       => [
                    'data-show-on' => '{"recommender_templateMode_1":"checked"}',
                ],
            ]
        );

        $builder->add('isPublished', YesNoButtonGroupType::class);

        if (!empty($options['update_select'])) {
            $builder->add(
                'buttons',
                FormButtonsType::class,
                [
                    'apply_text' => false,
                ]
            );
            $builder->add(
                'updateSelect',
                HiddenType::class,
                [
                    'data'   => $options['update_select'],
                    'mapped' => false,
                ]
            );
        } else {
            $builder->add(
                'buttons',
                FormButtonsType::class
            );
        }

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['update_select']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'recommender_templates';
    }

    private function getTemplateModes()
    {
        return [
            'mautic.plugin.recommender.form.basic' => 'basic',
            'mautic.plugin.recommender.form.html'  => 'html',
        ];
    }
}
