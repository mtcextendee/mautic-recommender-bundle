<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\LeadListQueryBuilderGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FilterFormSubscriber extends CommonSubscriber
{

    /**
     * FilterFormSubscriber constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {

        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_DISPLAY => [
                ['onFilterFormDisplay', 0],
            ]
        ];
    }

    /**
     * @param FilterFormEvent $event
     */
    public function onFilterFormDisplay(FilterFormEvent $event)
    {
        $choices = [
            'recommend_items_to_user' => 'mautic.plugin.recommender.form.type.recommend_items_to_user',
            'abandoned_cart'          => 'mautic.plugin.recommender.form.type.abandoned_cart',
            'advanced'                => 'mautic.plugin.recommender.form.type.advanced',
        ];

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE)) {
            $event = new FilterChoiceFormEvent($choices);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE, $event);
            $choices = $event->getChoices();
            unset($event);
        }

        $builder = $event->getBuilder();
        $builder->add(
            'type',
            'choice',
            [
                'choices'     => $choices,
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.plugin.recommender.form.recommendations.type',
                'label_attr'  => ['class' => ''],
                'empty_value' => false,
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

        /* $builder->add(
             'numberOfItems',
             NumberType::class,
             [
                 'label'       => 'mautic.plugin.recommender.form.number_of_items',
                 'label_attr'  => ['class' => 'control-label'],
                 'attr'        => [
                     'class'   => 'form-control',
                     'tooltip' => 'mautic.plugin.recommender.form.number_of_items.tooltip',
                 ],
                 'required'    => false,
                 'constraints' => [
                     new Range(
                         [
                             'min' => 1,
                         ]
                     ),
                 ],
             ]
         );*/

        $builder->add(
            'filter',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.form.type.filter',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                    'tooltip'=>'mautic.plugin.recommender.form.type.filter.tooltip',
                    'data-show-on' => '{"campaignevent_properties_type_type":["advanced"]}',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'booster',
            'text',
            [
                'label'      => 'mautic.plugin.recommender.form.type.booster',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                    'tooltip'=>'mautic.plugin.recommender.form.type.booster.tooltip',
                    'data-show-on' => '{"campaignevent_properties_type_type":["advanced"]}',

                ],
                'required'   => false,
            ]
        );
    }

}
