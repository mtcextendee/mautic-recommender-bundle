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

class FilterFormUserEventsSubscriber extends CommonSubscriber
{


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE => [
                ['onFilterFormChoicesGenerate', 5],
            ]
        ];
    }

    /**
     * @param FilterChoiceFormEvent $event
     */
    public function onFilterFormChoicesGenerate(FilterChoiceFormEvent $event)
    {
        $event->addChoice('user_events_filter', 'mautic.plugin.recommender.form.type.recommendations.by_points', 'points');


    }

}
