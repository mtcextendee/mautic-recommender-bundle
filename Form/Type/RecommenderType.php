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

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\LeadBundle\Form\DataTransformer\FieldFilterTransformer;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Choices;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class RecommenderType extends AbstractType
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /** @var  array */
    private $fieldChoices;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var RecommenderClientModel
     */
    private $recommenderClientModel;

    /**
     * @var Choices
     */
    private $choices;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RecommenderType constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager            $entityManager
     * @param TranslatorInterface      $translator
     * @param ListModel                $listModel
     * @param RecommenderClientModel   $recommenderClientModel
     * @param Choices                  $choices
     * @param RouterInterface          $router
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManager $entityManager,
        TranslatorInterface $translator,
        ListModel $listModel,
        RecommenderClientModel $recommenderClientModel,
        Choices $choices,
        RouterInterface $router
    ) {

        $this->dispatcher             = $dispatcher;
        $this->entityManager          = $entityManager;
        $this->translator             = $translator;
        $this->listModel              = $listModel;
        $this->recommenderClientModel = $recommenderClientModel;
        $this->choices                = $choices;
        $this->router                 = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'name',
            TextType::class,
            [
                'label'       => 'mautic.plugin.recommender.form.event.name',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class' => 'form-control',
                ],
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

        $transformer = new IdToEntityModelTransformer(
            $this->entityManager,
            'MauticRecommenderBundle:RecommenderTemplate',
            'id'
        );
        $builder->add(
            $builder->create(
                'template',
                ListTemplatesType::class,
                [
                    'multiple'    => false,
                    'label'       => 'mautic.plugin.recommender.template',
                    'attr'        => [
                        'class'    => 'form-control',
                        'onchange' => 'Mautic.disabledTemplateAction(window, this)',
                    ],
                    'required'    => true,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'mautic.core.value.required',
                            ]
                        ),
                    ],
                ]
            )->addModelTransformer($transformer)
        );

        if (!empty($options['update_select'])) {
            $windowUrl = $this->router->generate(
                'mautic_recommender_template_action',
                [
                    'objectAction' => 'new',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'newRecommenderButton',
                'button',
                [
                    'attr'  => [
                        'class'   => 'btn btn-primary btn-nospin',
                        'onclick' => 'Mautic.loadNewWindow({
                        "windowUrl": "'.$windowUrl.'"
                    })',
                        'icon'    => 'fa fa-plus',
                    ],
                    'label' => 'mautic.plugin.recommender.new.template',
                ]
            );

            // create button edit email
            $windowUrlEdit = $this->router->generate(
                'mautic_recommender_template_action',
                [
                    'objectAction' => 'edit',
                    'objectId'     => 'recommenderId',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'editRecommenderButton',
                'button',
                [
                    'attr'  => [
                        'class'    => 'btn btn-primary btn-nospin',
                        'onclick'  => 'Mautic.loadNewWindow(Mautic.standardRecommenderUrl({"windowUrl": "'.$windowUrlEdit.'","origin":"#'.$options['update_select'].'"}))',
                        'disabled' => empty($options['data']->getTemplate()),
                        'icon'     => 'fa fa-edit',
                    ],
                    'label' => 'mautic.plugin.recommender.edit.template',
                ]
            );
        }

        $choices = [];
        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE)) {
            $choiceEvent = new FilterChoiceFormEvent();
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE, $choiceEvent);
            $choices = $choiceEvent->getChoices('filter');
        }
        $builder->add(
            'filter',
            'choice',
            [
                'choices'     => $choices,
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.plugin.recommender.form.recommendations.type',
                'label_attr'  => ['class' => ''],
                'empty_value' => '',
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

        $this->fieldChoices     = $this->choices->addChoices('recommender_event');
        $filterModalTransformer = new FieldFilterTransformer($this->translator);
        $builder->add(
            $builder->create(
                'filters',
                'collection',
                [
                    'type'           => FilterType::class,
                    'options'        => [
                        'fields' => $this->fieldChoices,
                    ],
                    'error_bubbling' => false,
                    'mapped'         => true,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                    'constraints'    => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('mautic.plugin.recommender.form.filter.empty'),
                            ]
                        ),
                    ],
                ]
            )
                ->addModelTransformer($filterModalTransformer)
        );

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FORM_FILTER_GENERATE)) {
            $builderEvent = new FilterFormEvent($builder);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FORM_FILTER_GENERATE, $builderEvent);
            unset($builderEvent);
        }        

        // function
        $builder->add(
            'tableOrder',
            RecommenderTableOrderType::class,
            [
                'label'  => 'mautic.plugin.recommender.form.order_by',
                'fields' => $this->choices->getSelectOptions(),
            ]
        );

        $builder->add(
            'selfFilterMode',
            'choice',
            [
                'choices'     => [
                    'reflective' => 'mautic.plugin.recommender.form.self_filter_mode.reflective',
                    'exclusive' => 'mautic.plugin.recommender.form.self_filter_mode.exclusive',
                    'inclusive' => 'mautic.plugin.recommender.form.self_filter_mode.inclusive'
                ],
                'choice_attr' => function($choice, $key, $value) {                    
                    return ['tooltip' => "mautic.plugin.recommender.form.self_filter_mode.{$key}.tooltip"];
                },
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.plugin.recommender.form.self_filter_mode',
                'label_attr'  => ['class' => ''],
                'empty_value' => 'reflective',
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


        $builder->add(
            'buttons',
            'form_buttons'
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['fields'] = $this->fieldChoices;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['update_select']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender';
    }
}
