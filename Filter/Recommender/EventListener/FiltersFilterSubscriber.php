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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\RecommenderQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\FilterFactory;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class FiltersFilterSubscriber extends CommonSubscriber
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
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * PointsFilterSubscriber constructor.
     *
     * @param RecommenderClientModel  $clientModel
     * @param RecommenderQueryBuilder $recommenderQueryBuilder
     */
    public function __construct(RecommenderClientModel $clientModel, RecommenderQueryBuilder $recommenderQueryBuilder, FilterFactory $filterFactory)
    {
        $this->clientModel             = $clientModel;
        $this->recommenderQueryBuilder = $recommenderQueryBuilder;
        $this->filterFactory           = $filterFactory;
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

    /**
     * @param FilterChoiceFormEvent $event
     */
    public function onFilterFormChoicesGenerate(FilterChoiceFormEvent $event)
    {
        $event->addChoice('filter', 'mautic.plugin.recommender.form.type.filters', 'filters');
    }

    /**
     * @param FilterResultsEvent $event
     */
    public function onFilterResults(FilterResultsEvent $event)
    {
        /** @var RecommenderToken $recommenderToken */
        $recommenderToken = $event->getRecommenderToken();
        if ($recommenderToken->getRecommender()->getFilter() == self::TYPE) {
            $qb = $this->recommenderQueryBuilder->assembleContactQueryBuilder($recommenderToken);
            SqlQuery::debugQuery($qb);
            $results = $qb->execute()->fetchAll();
            foreach ($results as &$result) {
                $properties           = $this->getModel()->getItemPropertyValueRepository()->getValues($result['id']);
                $properties           = array_combine(array_column($properties, 'name'), array_column($properties, 'value'));
                $translatedProperties = [];
                foreach ($properties as $property=>$value) {
                    $translatedProperties[InputHelper::alphanum(InputHelper::transliterate($property))] = $value;
                }
                $result = array_merge($result, $translatedProperties);
            }

            $event->setItems($results);
        }
    }

    /**
     * @param LeadListFilteringEvent $event
     */
    public function onListFiltersFiltering(LeadListFilteringEvent $event)
    {
        $qb     = $event->getQueryBuilder();
        $filter = $event->getDetails();
        if (false !== strpos($filter['object'], 'recommender')) {
            $this->filterFactory->applySegmentQuery($filter, $qb, 'mautic.recommender.filter.recommender.dictionary');
            $event->setFilteringStatus(true);
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
