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

use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;

class Options
{
    /** @var RecommenderClientModel */
    protected $clientModel;

    /**
     * @var array
     */
    private $options;

    private $protectedKeys = ['itemId', 'userId', 'dateAdded', 'eventName', 'endpoint'];

    /**
     * @var Client
     */
    private $client;

    /**
     * Options constructor.
     */
    public function __construct(Client $client)
    {
        $this->client      = $client;
        $this->options     = $this->client->getOptions();
        $this->clientModel = $this->client->getClientModel();
    }

    /**
     * Get options with auto fill entities - userId, itemId...
     *
     * @param array $entities
     * @param array $addOptions
     *
     * @return array
     */
    public function getOptionsWithEntities($entities = [], $addOptions = [])
    {
        $options = $this->options;
        foreach ($entities as $entity) {
            switch ($entity) {
                case 'contactId':
                    if (!isset($entities['userId'])) {
                        $addOptions['lead'] = $this->clientModel->getCurrentContact();
                        unset($options['contactId']);
                    }
                    break;
            }

            if ('itemId' == $entity && !isset($options[$entity])) {
                throw new \Exception('Item ID param not exist');
                // die();
            }

            // don't convert not exist params
            if (!isset($options[$entity])) {
                continue;
            }

            switch ($entity) {
                case 'itemId':
                    $addOptions['item'] = $this->clientModel->getRepository()->findOneBy(['itemId' => $options[$entity]]);
                    if (!$addOptions['item']) {
                        throw new \Exception('Item ID '.$options[$entity].' not found');
                        //     die();
                    }
                    unset($options['itemId']);
                    break;
                case 'userId':
                    $addOptions['lead'] = $this->clientModel->getContactRepository()->getEntity($options[$entity]);
                    unset($options['userId']);
                    break;
                case 'dateAdded':
                    $addOptions['dateAdded'] = $options[$entity];
                    break;
            }
        }

        return array_merge($this->getOptions(), $addOptions);
    }

    /**
     * Get options - raw format.
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->options;

        foreach ($this->protectedKeys as $protectedKey) {
            if (isset($options[$protectedKey])) {
                unset($options[$protectedKey]);
            }
        }

        return $options;
    }

    public function addOption($key, $value)
    {
        if (!isset($this->options[$key])) {
            $this->options[$key] = $value;
        }
    }

    public function getOption($key)
    {
        if (isset($this->getOptions()[$key])) {
            return $this->getOptions()[$key];
        }
    }
}
