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
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Client
{
    /**
     * @var RecommenderClientModel
     */
    private $clientModel;
    private $options;
    private $settings = [];
    private $optionsResolver;
    private $endpoint;

    /**
     * Client constructor.
     */
    public function __construct(RecommenderClientModel $clientModel)
    {
        new PropertyAccessor();
        $this->clientModel      = $clientModel;
    }

    /**
     * @param string $endpoint
     *
     * @throws ApiEndpointNotFoundException
     */
    public function send($endpoint, array $options, $settings = [])
    {
        $this->endpoint        = $endpoint;
        $this->options         = $options;
        $this->settings        = $settings;
        $this->optionsResolver = new Options($this);
        $class                 = 'MauticPlugin\MauticRecommenderBundle\Api\Client\Request\\'.$endpoint;
        if (!class_exists($class)) {
            throw new ApiEndpointNotFoundException('Endpoint class '.$class.' doesn\'t exist.');
        }
        $loader = new $class($this);

        return $loader->run();
    }

    public function display(RecommenderToken $recommenderToken)
    {
        $this->endpoint        = $recommenderToken->getType();
        $this->options         = $recommenderToken->getOptions();
        $this->optionsResolver = new Options($this);
        $class                 = 'MauticPlugin\MauticRecommenderBundle\Api\Client\Request\\'.$this->endpoint;
        if (class_exists($class)) {
            $loader = new $class($this);

            return $loader->run();
        }
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
    public function getClientModel()
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

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
