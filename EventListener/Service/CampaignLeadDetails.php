<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener\Service;

use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Model\CampaignModel;

class CampaignLeadDetails
{
    /**
     * @var CampaignModel
     */
    private $campaignModel;

    /**
     * SecondsFromAddedToCampaign constructor.
     *
     * @param CampaignModel $campaignModel
     */
    public function __construct(CampaignModel $campaignModel)
    {
        $this->campaignModel = $campaignModel;
    }

    public function getDiffSecondsFromAddedTime(int $campaignId, int $leadId)
    {
        $leadCampaignRepo    = $this->campaignModel->getCampaignLeadRepository();
        $leadsCampaignDetail = $leadCampaignRepo->getLeadDetails($campaignId, [$leadId]);

        if (empty($leadsCampaignDetail[$leadId])) {
            return false;
        }

        $leadsCampaignDetail = end($leadsCampaignDetail[$leadId]);

        return (new \DateTime('now'))->getTimestamp() - $leadsCampaignDetail['dateAdded']->getTimestamp();
    }
}
