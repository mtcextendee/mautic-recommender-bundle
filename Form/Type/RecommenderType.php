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
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * RecommenderType constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager            $entityManager
     * @param TranslatorInterface      $translator
     * @param ListModel                $listModel
     * @param RecommenderClientModel   $recommenderClientModel
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $entityManager, TranslatorInterface $translator, ListModel $listModel, RecommenderClientModel $recommenderClientModel)
    {

        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->listModel = $listModel;
        $this->recommenderClientModel = $recommenderClientModel;
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
                    'class'   => 'form-control',
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    )
                ]
            ]
        );

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE)) {
            $choiceEvent = new FilterChoiceFormEvent();
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE, $choiceEvent);
        }
        $choices = $choiceEvent->getChoices('filter');
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

        $transformer = new IdToEntityModelTransformer($this->entityManager, 'MauticRecommenderBundle:RecommenderTemplate', 'id');
        $builder->add(
        $builder->create(
            'template',
            TemplatesListType::class,
            [
                'multiple'    => false,
                'label'       => 'mautic.plugin.recommender.template',
                'attr'        => [
                    'class'        => 'form-control',
                ],
            ]
        )->addModelTransformer($transformer)
        );

        $this->filterFieldChoices();
        $filterModalTransformer = new FieldFilterTransformer($this->translator);
        $builder->add(
            $builder->create(
                'filters',
                'collection',
                [
                    'type'    => FilterType::class,
                    'options' => [
                        'fields' => $this->fieldChoices,
                    ],
                    'error_bubbling' => false,
                    'mapped'         => true,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                ]
            )
                ->addModelTransformer($filterModalTransformer)
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

    private function filterFieldChoices()
    {
        $properties = $this->recommenderClientModel->getEventLogValueRepository()->getValueProperties();
        foreach ($properties as $property) {
            $type = RecommenderHelper::typeToTypeTranslator($property['type']);
            $this->fieldChoices['event_property'][$property['name']] = [
                'label'      => $property['name'],
                'properties' => [
                    'type' => $type,
                ],
                'icon'          => 'fa-question',
                'operators' => $this->listModel->getOperatorsForFieldType($type),
            ];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender';
    }
}
