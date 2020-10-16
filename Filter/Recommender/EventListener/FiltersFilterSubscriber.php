<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender\EventListener;

use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\RecommenderQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FiltersFilterSubscriber implements EventSubscriberInterface
{
    const TYPE = 'filters';

    /**
     * @var RecommenderClientModel
     */
    private $clientModel;

    /**
     * @var RecommenderQueryBuilder
     */
    private $recommenderQueryBuilder;

    /**
     * PointsFilterSubscriber constructor.
     */
    public function __construct(
        RecommenderClientModel $clientModel,
        RecommenderQueryBuilder $recommenderQueryBuilder,
        FilterFactory $filterFactory
    ) {
        $this->clientModel             = $clientModel;
        $this->recommenderQueryBuilder = $recommenderQueryBuilder;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE => [
                ['onFilterFormChoicesGenerate', 1],
            ],
            RecommenderEvents::ON_RECOMMENDER_FILTER_RESULTS               => [
                ['onFilterResults', 5],
            ],
        ];
    }

    public function onFilterFormChoicesGenerate(FilterChoiceFormEvent $event)
    {
        $event->addChoice('filter', 'mautic.plugin.recommender.form.type.filters', 'filters');
    }

    public function onFilterResults(FilterResultsEvent $event)
    {
        /** @var RecommenderToken $recommenderToken */
        $recommenderToken = $event->getRecommenderToken();
        $qb               = $this->recommenderQueryBuilder->assembleContactQueryBuilder($recommenderToken);
        SqlQuery::debugQuery($qb);
        $results = $qb->execute()->fetchAll();
        $results = array_slice($results, 0, $recommenderToken->getRecommender()->getNumberOfItems());
        foreach ($results as &$result) {
            $properties = $this->getModel()->getItemPropertyValueRepository()->getValues($result['id']);
            $properties = array_combine(array_column($properties, 'name'), array_column($properties, 'value'));
            if (!isset($properties['image'])) {
                $properties['image'] = '';
            }
            foreach ($properties as $alias => $property) {
                if ('price' === $alias) {
                    $properties[$alias] = number_format((float) $property, 2);
                }
            }
            $translatedProperties = [];
            foreach ($properties as $property => $value) {
                $translatedProperties[InputHelper::alphanum(InputHelper::transliterate($property))] = $value;
            }
            $result = array_merge($result, $translatedProperties);

            $event->setItems($results);
        }
    }

    /**
     * @return RecommenderClientModel
     */
    private function getModel()
    {
        return $this->clientModel;
    }
}
