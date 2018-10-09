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

use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RecommenderType.
 */
class RecommenderType extends AbstractType
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
     *
     * @param CorePermissions $security
     */
    public function __construct(CorePermissions $security, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->security   = $security;
        $this->router     = $router;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
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
                'required'    => true,
                'data'        => $options['data']->getNumberOfItems(),
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    ),
                    new Range(
                        [
                            'min' => 1,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'templateType',
            'button_group',
            [
                'label'      => 'mautic.plugin.recommender.form.template_type',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'mautic.plugin.recommender.form.template_type.tooltip',
                ],
                'choices' => [
                    'mautic.plugin.recommender.form.web'  => 'page',
                    'mautic.plugin.recommender.form.email'   => 'email',
                ],
                'choices_as_values' => true,
                'data'=> $options['data']->getTemplateType() ?:'web'
            ]
        );

        $builder->add(
            'templateMode',
            'button_group',
            [
                'label'      => 'mautic.plugin.recommender.form.template_mode',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'mautic.plugin.recommender.form.template_mode.tooltip',
                ],
                'choices' => [
                    'mautic.plugin.recommender.form.basic'  => 'basic',
                    'mautic.plugin.recommender.form.html'   => 'html',
                ],
                'choices_as_values' => true,
                'data'=> $options['data']->getTemplateMode() ?:'basic'
            ]
        );

        $builder->add(
            'properties',
            RecommenderPropertiesType::class,
            [
                'label' => false,
                'attr'       => [
                    'data-show-on' => '{"recommender_templateMode_0":"checked"}',
                ],
                'data'=>$options['data']->getProperties()
            ]
        );

        $builder->add(
            'template',
            RecommenderTemplateType::class,
            [
                'label' => 'mautic.plugin.recommender.template',
                'attr'       => [
                    'data-show-on' => '{"recommender_templateMode_1":"checked"}',
                ],
            ]
        );



        $builder->add('isPublished', 'yesno_button_group');

        $builder->add(
            'buttons',
            'form_buttons'
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender';
    }
}
