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
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommenderBundle\Event\FilterChoiceFormEvent;
use MauticPlugin\MauticRecommenderBundle\Event\FilterResultsEvent;
use MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class PointsFilterSubscriber extends CommonSubscriber
{
    CONST TYPE = 'points';

    /**
     * @var RecommenderClientModel
     */
    private $clientModel;


    /**
     * PointsFilterSubscriber constructor.
     *
     * @param RecommenderClientModel $clientModel
     */
    public function __construct(RecommenderClientModel $clientModel)
    {

        $this->clientModel = $clientModel;
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
        $event->addChoice('type', 'mautic.plugin.recommender.form.type.points', 'points');
    }

    /**
     * @param FilterResultsEvent $event
     */
    public function onFilterResults(FilterResultsEvent $event)
    {
        /** @var RecommenderToken $recommenderToken */
        $recommenderToken = $event->getRecommenderToken();
        if ($recommenderToken->getType() == self::TYPE) {
            $results = $this->getModel()->getRepository()->getContactsItemsByPoints($recommenderToken->getUserId(), $recommenderToken->getLimit());
            foreach ($results as &$result) {
                $properties = $this->getModel()->getItemPropertyValueRepository()->getValues($result['id']);;
                $properties = array_combine(array_column($properties, 'name'), array_column($properties, 'value'));
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
     * @return RecommenderClientModel
     */
    private function getModel()
    {
        return $this->clientModel;
    }

}
