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
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Client
{
    private $propertyAccessor;

    /**
     * @var RecommenderClientModel
     */
    private $clientModel;

    private $options;
    private $optionsResolver;



    private $endpoint;

    /**
     * Client constructor.
     *
     * @param RecommenderClientModel $clientModel
     *
     */
    public function __construct(RecommenderClientModel $clientModel)
    {
        $this->propertyAccessor = new PropertyAccessor();
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
        $this->endpoint = $endpoint;
        $this->options = $options;
        $this->optionsResolver = new Options($this);
        $class = 'MauticPlugin\MauticRecommenderBundle\Api\Client\Request\\'.$endpoint;
        if (!class_exists($class)) {
            throw new ApiEndpointNotFoundException('Class '.$class.' doesn\'t exist.');
        }
        $loader = new $class($this);
        $loader->run();
        return $loader;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Options
     */
    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }


    /**
     * @return RecommenderClientModel
     */
    public function getClientModel(): RecommenderClientModel
    {
        return $this->clientModel;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}

