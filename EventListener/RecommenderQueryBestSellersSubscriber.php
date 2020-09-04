<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use MauticPlugin\MauticRecommenderBundle\Enum\FiltersEnum;
use MauticPlugin\MauticRecommenderBundle\Event\RecommenderQueryBuildEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecommenderQueryBestSellersSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_BUILD_QUERY   => ['onRecommenderQueryBuild', 0],
        ];
    }

    public function onRecommenderQueryBuild(RecommenderQueryBuildEvent $queryBuildEvent)
    {
        $recommender  = $queryBuildEvent->getRecommenderToken()->getRecommender();
        $queryBuilder = $queryBuildEvent->getQueryBuilder();

        if ($recommender->getFilterTarget() === FiltersEnum::BEST_SELLERS) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('l.event_id', 4));
            $queryBuilder->orderBy('COUNT(l.event_id)', 'DESC');
        }
    }
}
