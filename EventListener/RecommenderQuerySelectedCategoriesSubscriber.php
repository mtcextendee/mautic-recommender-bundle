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

use Mautic\CoreBundle\Helper\ArrayHelper;
use MauticPlugin\MauticRecommenderBundle\Enum\FiltersEnum;
use MauticPlugin\MauticRecommenderBundle\Event\RecommenderQueryBuildEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecommenderQuerySelectedCategoriesSubscriber implements EventSubscriberInterface
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

        $categories = ArrayHelper::getValue('categories', $recommender->getProperties());

        if (!empty($categories)) {
            $categories = array_map(
                function ($category) use ($queryBuilder) {
                    return $queryBuilder->expr()->literal($category);
                },
                $categories
            );

            $queryBuilder->innerJoin(
                'ri',
                MAUTIC_TABLE_PREFIX.'recommender_item_property_value',
                'ripv',
                'ri.id = ripv.item_id AND ripv.property_id = 4'
            );
            /* $queryBuilder->andWhere(
                 $queryBuilder->expr()->in('ripv.value', array_map([$queryBuilder->expr(), 'literal'], $categories))
             );*/

            $queryBuilder->andWhere($queryBuilder->expr()->in('ripv.value', $categories));
        }
    }
}
