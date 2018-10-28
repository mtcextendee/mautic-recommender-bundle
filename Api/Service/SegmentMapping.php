<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Service;

use Mautic\LeadBundle\Model\ListModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use Psr\Log\LoggerInterface;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;
use Recurr\Transformer\TranslatorInterface;

class SegmentMapping
{

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * ApiCommands constructor.
     *
     * @param ListModel         $listModel
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(
        ListModel $listModel,
        IntegrationHelper $integrationHelper
    ) {

        $this->listModel         = $listModel;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * @param $apiRequest
     * @param $userId
     */
    public function map($apiRequest, $userId)
    {
        $lead['id'] = $userId;
        $settings   = $this->integrationHelper->getIntegrationObject('RecommenderTemplate')->getIntegrationSettings(
        )->getFeatureSettings();


        if (empty($settings['abandoned_cart'])) {
            return;
        }

        if (!in_array($apiRequest, ['AddCartAddition', 'AddPurchase'])) {
            return;
        }

        switch ($apiRequest) {
            case "AddCartAddition":
                if (!empty($settings['abandoned_cart_segment'])) {
                    $this->listModel->addLead($lead, [$settings['abandoned_cart_segment']]);
                }
                break;
            case "AddPurchase":
                if (!empty($settings['abandoned_cart_order_segment'])) {
                    $this->listModel->addLead($lead, [$settings['abandoned_cart_order_segment']]);
                }
                if (!empty($settings['abandoned_cart_order_segment_remove'])) {
                    $this->listModel->removeLead($lead, [$settings['abandoned_cart_order_segment_remove']]);
                }
                break;
        }
    }
}

