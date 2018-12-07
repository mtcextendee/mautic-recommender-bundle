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
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FilterResultsSubscriber extends CommonSubscriber
{

    /**
     * @var ApiCommands
     */
    private $apiCommands;

    /**
     * FilterResultsSubscriber constructor.
     *
     * @param ApiCommands $apiCommands
     */
    public function __construct(ApiCommands $apiCommands)
    {

        $this->apiCommands = $apiCommands;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS => [
                ['onFilterResults', 0],
            ]
        ];
    }

    /**
     * @param FilterResultsEvent $event
     */
    public function onFilterResults(FilterResultsEvent $event)
    {
        $event->setItems($this->apiCommands->getResults($event->getRecommenderToken()));

    }

}
