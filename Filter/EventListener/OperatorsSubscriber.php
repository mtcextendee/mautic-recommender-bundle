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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadListDictionaryGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;

class OperatorsSubscriber extends CommonSubscriber
{
    /**
     * OperatorsSubscriber constructor.
     *
     * @param RecommenderDictionary $recommenderDictionary
     * @param SegmentDictionary     $segmentDictionary
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_FILTERS_DICTIONARY_GENERATED   => ['onDictionaryGenerated', 1],
        ];
    }

    /**
     * @param LeadListDictionaryGeneratedEvent $event
     */
    public function onDictionaryGenerated(LeadListDictionaryGeneratedEvent $event)
    {
        // $dictionaries = $this->recommenderDictionary->getDictionary();
     /*   $dictionaries = [];
        foreach ($dictionaries as $key=>$dictionary) {
            $event->addTranslation($key, $dictionary);
        }*/
    }
}
