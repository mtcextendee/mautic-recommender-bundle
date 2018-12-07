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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

        $builder = $event->getBuilder();

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE)) {
            $choiceEvent = new FilterChoiceFormEvent();
            $choiceEvent->addChoice('type', 'mautic.plugin.recommender.form.type.recommend_items_to_user_events', 'user_events');
            $choiceEvent->addChoice('type', 'mautic.plugin.recommender.form.type.recommend_items_to_items_events', 'item_events');
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE, $choiceEvent);
        }

        $choices = $choiceEvent->getChoices('type');
        $builder->add(
            'type',
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

        $choices =  $choiceEvent->getChoices('user_events_filter');

        $builder->add(
            'user_events_filter',
            'choice',
            [
                'choices'     => $choices,
                'expanded'    => false,
                'multiple'    => false,
                'label'       => '',
                'label_attr'  => ['class' => ''],
                'empty_value' => '',
                'required'    => true,
                'attr'=>[
                    'data-show-on' => '{"campaignevent_properties_type_type":"user_events"}',
                ],
            ]
        );

        $choices =  $choiceEvent->getChoices('user_events_filter');

        $builder->add(
            'item_events_filter',
            'choice',
            [
                'choices'     => $choices,
                'expanded'    => false,
                'multiple'    => false,
                'label'       => '',
                'label_attr'  => ['class' => ''],
                'required'    => true,
                'empty_value' => '',
                'attr'=>[
                    'data-show-on' => '{"campaignevent_properties_type_type":"item_events"}',
                ],
            ]
        );

        $options = $builder->getData();
        print_r($options);
        $builder->add(
            'daterange_filter',
            'choice',
            [
                'choices' => [
                    'midnight'  => 'mautic.core.daterange.0days',
                    '-24 hours' => 'mautic.core.daterange.1days',
                    '-1 week'   => $this->translator->transChoice('mautic.core.daterange.week', 1, ['%count%' => 1]),
                    '-2 weeks'  => $this->translator->transChoice('mautic.core.daterange.week', 2, ['%count%' => 2]),
                    '-3 weeks'  => $this->translator->transChoice('mautic.core.daterange.week', 3, ['%count%' => 3]),
                    '-1 month'  => $this->translator->transChoice('mautic.core.daterange.month', 1, ['%count%' => 1]),
                    '-2 months' => $this->translator->transChoice('mautic.core.daterange.month', 2, ['%count%' => 2]),
                    '-3 months' => $this->translator->transChoice('mautic.core.daterange.month', 3, ['%count%' => 3]),
                    '-1 year'   => $this->translator->transChoice('mautic.core.daterange.year', 1, ['%count%' => 1]),
                    '-2 years'  => $this->translator->transChoice('mautic.core.daterange.year', 2, ['%count%' => 2]),
                ],
                'expanded'   => false,
                'multiple'   => false,
                'label'      => 'mautic.plugin.recommender.form.type.date_range',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
                'empty_value' => false,
                //'data'=> isset($options['data']['daterange_filter']) ? $options['data']['daterange_filter'] : '-1 month',
            ]
        );

    }

}
