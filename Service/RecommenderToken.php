<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Service;

use Mautic\CampaignBundle\Model\CampaignModel;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel;
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;

class RecommenderToken
{
    private $id;

    private $userId;

    /**
     * @var LeadModel
     */
    private $leadModel;

    private $content = '';

    /**
     * @var Recommender
     */
    private $recommender;

    /**
     * @var RecommenderModel
     */
    private $recommenderModel;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var array
     */
    private $filterTokens = [];

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    /**
     * RecommenderToken constructor.
     */
    public function __construct(RecommenderModel $recommenderModel, IntegrationHelper $integrationHelper, ContactTracker $contactTracker)
    {
        $this->recommenderModel  = $recommenderModel;
        $this->integrationHelper = $integrationHelper;
        $this->contactTracker    = $contactTracker;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        if (!$this->userId) {
            if ($lead = $this->contactTracker->getContact()) {
                return $lead->getId();
            }
        }

        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int|mixed
     */
    public function getLimit()
    {
        return $this->getRecommender()->getNumberOfItems();
    }

    /**
     * @return Recommender
     */
    public function getRecommender()
    {
        if ($this->id && (!$this->recommender || ($this->recommender && $this->recommender->getId() != $this->id))) {
            $this->recommender = $this->recommenderModel->getEntity($this->id);
        }

        return $this->recommender;
    }

    /**
     * @param Recommender $recommender
     */
    public function setRecommender($recommender)
    {
        $this->recommender = $recommender;
    }

    /**
     * Get settings from plugins.
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->integrationHelper->getIntegrationObject('Recommender')->getIntegrationSettings()->getFeatureSettings();
    }

    /**
     * @param $token
     * @param $value
     */
    public function addFilterToken($token, $value)
    {
        $this->filterTokens[$token] = $value;
    }

    /**
     * @return array
     */
    public function getFilterTokens()
    {
        return $this->filterTokens;
    }

    /**
     * @param array $filterTokens
     */
    public function setFilterTokens($filterTokens)
    {
        $this->filterTokens = $filterTokens;
    }

    public function reset()
    {
        $this->filterTokens = [];
    }

    public function getFilters(): array
    {
        $filters = $this->getRecommender()->getFilters();

        foreach ($this->filterTokens as $token => $value) {
            foreach ($filters as $key => $filter) {
                if (isset($filter['filter'])) {
                    if (!is_array($filter['filter'])) {
                        $filters[$key]['filter'] = str_replace($token, $value, $filter['filter']);
                    }
                }
            }
        }

        return $filters;
    }
}
