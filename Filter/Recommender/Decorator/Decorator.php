<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Decorator;


use Mautic\LeadBundle\Segment\ContactSegmentFilterOperator;
use Mautic\LeadBundle\Segment\Decorator\CustomMappedDecorator;
use Mautic\LeadBundle\Services\ContactSegmentFilterDictionary;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class Decorator extends CustomMappedDecorator
{
    /** @var  RecommenderToken */
    private $recommenderToken;

    /**
     * Decorator constructor.
     *
     * @param ContactSegmentFilterOperator   $contactSegmentFilterOperator
     * @param ContactSegmentFilterDictionary $contactSegmentFilterDictionary
     */
    public function __construct(
        ContactSegmentFilterOperator $contactSegmentFilterOperator,
        ContactSegmentFilterDictionary $contactSegmentFilterDictionary,
        Dictionary $dictionary
    ) {
        parent::__construct($contactSegmentFilterOperator, $contactSegmentFilterDictionary);
        $this->dictionary = $dictionary->getDictionary();
    }

    /**
     * @return string
     */
    public function getRelationJoinTable()
    {
        return MAUTIC_TABLE_PREFIX.'recommender_event_log';
    }

    /**
     * @return string
     */
    public function getRelationJoinTableField()
    {
        return 'event_log_id';
    }

    /**
     * @return RecommenderToken
     */
    public function getRecommenderToken()
    {
        return $this->recommenderToken;
    }

    /**
     * @param RecommenderToken $recommenderToken
     */
    public function setRecommenderToken($recommenderToken)
    {
        $this->recommenderToken = $recommenderToken;
    }

}
