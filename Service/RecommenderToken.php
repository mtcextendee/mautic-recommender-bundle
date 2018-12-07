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
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;

class RecommenderToken
{

    private $id;

    private $userId;

    private $type;
    /**
     * @var TemplateModel
     */
    private $recommenderModel;

    /**
     * @var CampaignModel
     */
    private $campaignModel;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /** @var  string */
    private $source;

    /** @var  int */
    private $sourceId;

    /** @var array  */
    private $properties = [];

    private $content = '';

    /** @var  RecommenderTemplate */
    private $template;


    /**
     * RecommenderToken constructor.
     *
     * @param TemplateModel $recommenderModel
     * @param LeadModel     $leadModel
     * @param CampaignModel $campaignModel
     */
    public function __construct(TemplateModel $recommenderModel, LeadModel $leadModel, CampaignModel $campaignModel)
    {
        $this->recommenderModel  = $recommenderModel;
        $this->campaignModel = $campaignModel;
        $this->leadModel = $leadModel;
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        // default type
        if (!$this->type) {
            return 'RecommendItemsToUser';
        }

        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
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
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return RecommenderTemplate|null
     */
    public function getTemplate()
    {
        if ($this->id && (!$this->template || ($this->template && $this->template->getId() != $this->id))) {
            return $this->recommenderModel->getEntity($this->id);
        }
    }


    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }


    /**
     * @param int $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param int    $userId
     * @param string $source
     * @param int    $sourceId
     * @param array  $properties
     * @param string $content
     */
    public function setConfig($userId, $source, $sourceId, $properties, $content)
    {
        $this->setUserId($userId);
        $this->setSource($source);
        $this->setSourceId($sourceId);
        $this->setProperties($properties);
        $this->setContent($content);
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

    public function getOptions()
    {
        $this->properties['userId'] = $this->userId;
        $this->properties['limit'] = $this->getLimit();
        return $this->properties;
    }

    /**
     * @return int|mixed
     */
    private function getLimit()
    {
        return $this->getTemplate()->getNumberOfItems();
    }
}

