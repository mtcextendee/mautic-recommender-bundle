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

use MauticPlugin\MauticRecommender\Exception\ApiEndpointNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyValueModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Client
{
    private $propertyAccessor;

    /**
     * @var ItemModel
     */
    private $itemModel;

    /**
     * @var ItemPropertyModel
     */
    private $itemPropertyModel;

    /**
     * @var ItemPropertyValueModel
     */
    private $itemPropertyValueModel;

    /**
     * Client constructor.
     *
     * @param ItemModel              $itemModel
     * @param ItemPropertyModel      $itemPropertyModel
     * @param ItemPropertyValueModel $itemPropertyValueModel
     */
    public function __construct(ItemModel $itemModel, ItemPropertyModel $itemPropertyModel, ItemPropertyValueModel $itemPropertyValueModel)
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->itemModel = $itemModel;
        $this->itemPropertyModel = $itemPropertyModel;
        $this->itemPropertyValueModel = $itemPropertyValueModel;
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @throws ApiEndpointNotFoundException
     */
    public function send($endpoint, array $options)
    {
        if (!isset($options[0])) {
            $options = [$options];
        }
        $class = 'MauticPlugin\MauticRecommenderBundle\Api\Client\Request\\'.$endpoint;
        if (!class_exists($class)) {
            throw new ApiEndpointNotFoundException('Class '.$class.' doesn\'t exist.');
        }
        switch ($endpoint) {
            case "AddItem":
                $loader = new $class($options, $this->itemModel);
                break;
            case "AddItemProperty":
                $loader = new $class($options, $this->itemPropertyModel);
                break;
            case "AddItemPropertyValue":
                $loader = new $class($options, $this->itemPropertyValueModel);
                break;
        }

        $loader->execute();
    }
}

