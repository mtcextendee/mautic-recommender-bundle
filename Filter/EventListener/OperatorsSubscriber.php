<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\EventListener;

use Mautic\LeadBundle\Event\LeadListDictionaryGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OperatorsSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_FILTERS_DICTIONARY_GENERATED   => ['onDictionaryGenerated', 1],
        ];
    }

    public function onDictionaryGenerated(LeadListDictionaryGeneratedEvent $event)
    {
        // $dictionaries = $this->recommenderDictionary->getDictionary();
     /*   $dictionaries = [];
        foreach ($dictionaries as $key=>$dictionary) {
            $event->addTranslation($key, $dictionary);
        }*/
    }
}
