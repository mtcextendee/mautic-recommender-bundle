<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener\Filters;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;

class AbandonedCartFilterSubscriber extends CommonSubscriber
{
    CONST TYPE = 'abandoned_cart';
    /**
     * @var CampaignLeadDetails
     */
    private $campaignLeadDetails;

    /**
     * AbandonedCartFilterSubscriber constructor.
     *
     * @param CampaignLeadDetails $campaignLeadDetails
     */
    public function __construct(CampaignLeadDetails $campaignLeadDetails)
    {

        $this->campaignLeadDetails = $campaignLeadDetails;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE => [
                ['onFilterFormChoicesGenerate', 0],
            ],
            RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS               => [
                ['onFilterResults', -5],
            ],
        ];
    }

    /**
     * @param FilterChoiceFormEvent $event
     */
    public function onFilterFormChoicesGenerate(FilterChoiceFormEvent $event)
    {
        $event->addChoice('type', 'mautic.plugin.recommender.form.type.abandoned_cart', 'abandoned_cart');
    }

    /**
     * @param FilterResultsEvent $event
     */
    public function onFilterResults(FilterResultsEvent $event)
    {
        $recombeeTokne = $event->getRecommenderToken();
        if ($recombeeTokne->getType() == self::TYPE) {
            if ('campaign' === $recombeeTokne->getSource()) {

            }
        }
    }

}
