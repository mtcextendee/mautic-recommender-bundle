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
use MauticPlugin\MauticRecommenderBundle\Filter\Token\ContextToken;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecommenderQueryContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContextToken
     */
    private $contextToken;

    /**
     * RecommenderQueryContextSubscriber constructor.
     */
    public function __construct(ContextToken $contextToken)
    {
        $this->contextToken = $contextToken;
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
        $recommenderToken = $queryBuildEvent->getRecommenderToken();
        $recommender      = $recommenderToken->getRecommender();
        $queryBuilder     = $queryBuildEvent->getQueryBuilder();
        $filters          = $recommender->getFilters();
        foreach ($filters as $filter) {
            if (isset($filter['filter']) && !is_array($filter['filter']) && 0 === strpos($filter['filter'], '{', 0)) {
                if (0 === strpos($filter['filter'], '{context', 0)) {
                    $tokenValue = $this->contextToken->findValueFromContext(
                        $filter['filter'],
                        $queryBuilder,
                        $recommenderToken
                    );
                    $recommenderToken->addFilterToken(
                        $filter['filter'],
                        $tokenValue
                    );
                }
            }
        }
    }
}
