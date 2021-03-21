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
use MauticPlugin\MauticRecommenderBundle\Integration\RecommenderProperties;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecommenderQueryAbandonedCartSubscriber implements EventSubscriberInterface
{
    /**
     * @var RecommenderProperties
     */
    private $recommenderProperties;

    public function __construct(RecommenderProperties $recommenderProperties)
    {
        $this->recommenderProperties = $recommenderProperties;
    }

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
        if (FiltersEnum::ABANDONED_CART === $recommender->getFilterTarget()) {
            if ($contactId = $queryBuildEvent->getRecommenderToken()->getUserId()) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq('l.lead_id', (int) $contactId),
                    $queryBuilder->expr()->eq('l.event_id', $this->recommenderProperties->getAddToCartEventId())
                );

                $tableAlias  = 'rel2';

                $subQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
                $subQueryBuilder
                    ->select('NULL')->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', $tableAlias)
                    ->andWhere($tableAlias.'.lead_id = l.lead_id')
                    ->andWhere($tableAlias.'.id > l.id')
                    ->andWhere(
                        sprintf(
                            "(%s.event_id = %s AND %s.item_id = l.item_id) or %s.event_id = %s",
                            $tableAlias,
                            $this->recommenderProperties->getRemoveFromCartEventId(),
                            $tableAlias,
                            $tableAlias,
                            $this->recommenderProperties->getPurchaseEventId()
                        )
                    );

                $queryBuilder->andWhere($queryBuilder->expr()->notExists($subQueryBuilder->getSQL()));
            }
        }
    }
}
