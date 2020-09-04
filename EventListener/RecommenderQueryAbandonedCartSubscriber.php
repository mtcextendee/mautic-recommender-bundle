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

class RecommenderQueryAbandonedCartSubscriber implements EventSubscriberInterface
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
        if ($recommender->getFilterTarget() === FiltersEnum::ABANDONED_CART) {
            if ($contactId = $queryBuildEvent->getRecommenderToken()->getUserId()) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('l.lead_id', (int) $contactId));

                $tableAlias  = 'rel2';
                $tableAlias2 = 'rel3';

                $leftJoinCondition = sprintf(
                    '%s
                .lead_id = %s.lead_id AND %s.id > %s.id  AND  ((%s.item_id = %s.item_id AND  %s.event_id  = 3) OR  %s.event_id = 4)',
                    $tableAlias2,
                    $tableAlias,
                    $tableAlias2,
                    $tableAlias,
                    $tableAlias,
                    $tableAlias2,
                    $tableAlias2,
                    $tableAlias2
                );

                $subQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
                $subQueryBuilder
                    ->select('NULL')->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', $tableAlias)
                    ->leftJoin($tableAlias, MAUTIC_TABLE_PREFIX.'recommender_event_log', $tableAlias2,
                        $leftJoinCondition
                    )
                    ->andWhere($tableAlias.'.event_id = 2')
                    ->andWhere($tableAlias.'.item_id = l.item_id')
                    ->andWhere($tableAlias2.'.id IS NULL');

                $queryBuilder->andWhere($queryBuilder->expr()->exists($subQueryBuilder->getSQL()));
            }
        }
    }
}
