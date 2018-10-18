<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Client;

use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecommender\Exception\ApiEndpointNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\EventLogModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Client
{
    private $propertyAccessor;

    /**
     * @var ItemModel
     */
    private $itemModel;

    /**
     * @var EventLogModel
     */
    private $eventLogModel;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * Client constructor.
     *
     * @param ItemModel                    $itemModel
     * @param EventLogModel $eventLogModel
     * @param LeadModel                    $leadModel
     */
    public function __construct(ItemModel $itemModel, EventLogModel $eventLogModel, LeadModel $leadModel)
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->itemModel        = $itemModel;
        $this->eventLogModel    = $eventLogModel;
        $this->leadModel = $leadModel;
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @throws ApiEndpointNotFoundException
     */
    public function send($endpoint, array $options)
    {
        $this->optionCleanUp($options);
        if (empty($options)) {
            die('options empty');
        }
        $class = 'MauticPlugin\MauticRecommenderBundle\Api\Client\Request\\'.$endpoint;
        if (!class_exists($class)) {
            throw new ApiEndpointNotFoundException('Class '.$class.' doesn\'t exist.');
        }
        switch ($endpoint) {
            case "AddItem":
            case "AddItemProperty":
            case "AddItemPropertyValue":
                $loader = new $class($options, $this->itemModel);
                break;
            case "AddDetailView":
                $loader = new $class($options, $this->eventLogModel);
                break;
        }

        $loader->execute();
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function optionCleanUp(array &$options)
    {
        $resetOptions = reset($options);
        if (is_array($resetOptions)) {
            $options = array_values($options);
        }else{
            $options = [$options];
        }
        foreach ($options as $key => &$option) {
            if (isset($option['id'])) {
                $option['itemId'] = $option['id'];
                unset($option['id']);
            }
            if (!isset($option['itemId'])) {
                unset($options[$key]);
                continue;
            }

            if (isset($option['userId'])) {
                $option['lead'] = $this->leadModel->getEntity($option['userId']);
                unset($option['userId']);
            }


            if (!isset($option['itemId'])) {
                $option['item'] = $this->itemModel->getRepository()->findBy(['item_id' => $option['itemId']]);
                unset($option['itemId']);
            }
        }
    }
}

