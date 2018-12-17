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
use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\DynamicContentBundle\Form\Type\DwcEntryFiltersType;
use Mautic\LeadBundle\Form\DataTransformer\FieldFilterTransformer;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
     * RecommenderType constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager            $entityManager
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $entityManager)
    {

        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
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

      //  $filterModalTransformer = new FieldFilterTransformer($this->translator);
        $builder->add(
            $builder->create(
                'filters',
                'collection',
                [
                    'type'    => DwcEntryFiltersType::class,
                    'options' => [
                        'countries'    => [],
                        'regions'      => [],
                        'timezones'    => [],
                        'locales'      => [],
                        'fields'       => [
                            'test'=>[
                            'date_added' => [
                            'label'      => 'nothing',
                            'properties' => ['type' => 'date'],
                            'operators'  => [
                                '=',
                                '!=',
                                'empty',],
                            'object'     => 'test',
                        ]
                            ]
                        ],
                        'deviceTypes'  => [],
                        'deviceBrands' => [],
                        'deviceOs'     => [],
                        'tags'         => [],
                    ],
                    'error_bubbling' => false,
                    'mapped'         => true,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                ]
            )
                //->addModelTransformer($filterModalTransformer)
        );



        $builder->add(
            'buttons',
            'form_buttons'
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender';
    }
}
