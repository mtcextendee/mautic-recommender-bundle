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
     * RecommenderToken constructor.
     *
     * @param RecommenderModel  $recommenderModel
     * @param LeadModel         $leadModel
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(RecommenderModel $recommenderModel, LeadModel $leadModel, IntegrationHelper $integrationHelper)
    {
        $this->leadModel         = $leadModel;
        $this->recommenderModel  = $recommenderModel;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        if (!$this->userId) {
            if ($lead = $this->leadModel->getCurrentLead()) {
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
}
