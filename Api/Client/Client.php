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
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Client
{
    private $propertyAccessor;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var RecommenderClientModel
     */
    private $clientModel;

    /**
     * Client constructor.
     *
     * @param RecommenderClientModel $clientModel
     * @param LeadModel   $leadModel
     *
     */
    public function __construct(RecommenderClientModel $clientModel, LeadModel $leadModel)
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->leadModel = $leadModel;
        $this->clientModel = $clientModel;
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
            case "AddProperty":
            case "AddItemPropertyValue":
            case "AddEvent":
            case "AddEventLog":
            case "AddEventLogPropertyValue":
                $loader = new $class($options, $this->clientModel, $this);
                break;
            case "AddDetailView":
                $this->mapOptionsToEntity($options, ['contact', 'item', 'name'], $endpoint);
                $loader = new $class($options, $this->clientModel, $this);
                break;
        }

        $loader->execute();
        return $loader;
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
        }
    }

    /**
     * @param array  $options
     * @param array  $entities
     * @param string $endpoint
     *
     * @return array
     */
    private function mapOptionsToEntity(array &$options, $entities = [], $endpoint = '')
    {
        foreach ($options as $key => &$option) {

            if (!isset($option['name']) && in_array('name', $entities)) {
                $option['name'] = $endpoint;
            }
            if (isset($option['userId']) && in_array('contact', $entities)) {
                $option['lead'] = $this->leadModel->getEntity($option['userId']);
            }

            if (isset($option['itemId']) && in_array('item', $entities)) {
                $option['item'] = $this->clientModel->getRepository()->findOneBy(['itemId' => $option['itemId']]);
            }
        }
    }
}

