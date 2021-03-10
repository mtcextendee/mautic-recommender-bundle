<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Client\Request;

use MauticPlugin\MauticRecommenderBundle\Api\Client\Client;
use MauticPlugin\MauticRecommenderBundle\Api\Client\Options;
use MauticPlugin\MauticRecommenderBundle\Model\EventLogModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRequest
{
    /** @var   */
    protected $options;
    protected $optionsResolver;
    protected $option = [];
    protected $repo;

    /**
     * @var
     */
    private $model;

    /**
     * @var Client
     */
    private $client;

    /** @var array */
    protected $entities = [];

    /** @var array */
    private $deleteEntities = [];

    /** @var PropertyAccessor */
    private $accessor;

    /**
     * ItemRequest constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client          = $client;
        $this->options         = $this->client->getOptions();
        $this->optionsResolver = $this->client->getOptionsResolver();
        $this->model           = $this->client->getClientModel();
        $this->accessor        = new PropertyAccessor();
    }

    /**
     * New entity.
     */
    protected function newEntity()
    {
    }

    protected function find()
    {
        return false;
    }

    /**
     *  Run script.
     */
    public function run()
    {
        return $this;
    }

    public function add()
    {
        $newEntity = $this->newEntity();
        $this->addEntity($this->setValues($newEntity));

        return $newEntity;
    }

    public function addIfNotExist()
    {
        if ($entity = $this->find()) {
            return $entity;
        }

        $newEntity  =  $this->newEntity();
        $this->addEntity($this->setValues($newEntity));

        return $newEntity;
    }

    public function addOrEditIfExist()
    {
        if ($entity = $this->find()) {
            return $this->edit();
        }

        $newEntity  =  $this->newEntity();
        $this->addEntity($this->setValues($newEntity));

        return $newEntity;
    }

    public function edit()
    {
        $entity = $this->find();
        $this->addEntity($this->setValues($entity));

        return $entity;
    }

    public function save()
    {
        if (count($this->getEntities()) == 1) {
            return $this->getRepo()->saveEntity($this->getEntities()[0]);
        } elseif (count($this->getEntities()) > 1) {
            return $this->getRepo()->saveEntities($this->getEntities());
        }
    }

    public function delete()
    {
        if (count($this->getDeleteEntities()) == 1) {
            return $this->getRepo()->deleteEntity($this->getDeleteEntities()[0]);
        } elseif (count($this->getDeleteEntities()) > 1) {
            return $this->getRepo()->deleteEntities($this->getDeleteEntities());
        }
    }

    /**
     * @param object $entity
     *
     * @return object
     */
    public function setValues($entity)
    {
        foreach ($this->getOptions() as $key=>$value) {
            try {
                $this->accessor->setValue($entity, $key, $value);
            } catch (\Exception $exception) {
            }
        }

        return $entity;
    }

    /**
     * @return mixed
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return RecommenderClientModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param object $entity
     */
    public function addEntity($entity)
    {
        $this->entities[] = $entity;
    }

    /**
     * @return array
     */
    public function getDeleteEntities()
    {
        return $this->deleteEntities;
    }

    /**
     * @param object $entity
     */
    public function addDeleteEntity($entity)
    {
        $this->deleteEntities[] = $entity;
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
     * Return property type.
     *
     * @param $property
     *
     * @return string
     */
    public function getPropertyType($property)
    {
        if (is_array($property)) {
            return 'select';
        } elseif (in_array($property, [false, true, 'false', 'true'], true)) {
            return 'bool';
        } elseif (is_numeric($property)) {
            return 'int';
        } elseif (is_double($property)) {
            return 'float';
        } elseif (is_bool($property)) {
            return 'bool';
        } elseif ($this->isDateTime($property)) {
            return 'datetime';
        } else {
            return 'text';
        }
    }

    /**
     * @param $date
     *
     * @return bool
     */
    private function isDateTime($date)
    {
        $d  = \DateTime::createFromFormat('Y-m-d g:i:s', $date);
        $d2 = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if (($d && $d->format('Y-m-d g:i:s') == $date) || ($d2 && $d2->format('Y-m-d H:i:s') == $date)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if has settings by client.
     *
     * @param $setting
     *
     * @return bool
     */
    public function hasSetting($setting)
    {
        if (isset($this->getClient()->getSettings()[$setting])) {
            return true;
        }

        return false;
    }

    public function getSetting($setting)
    {
        if ($this->hasSetting($setting)) {
            return $this->getClient()->getSettings()[$setting];
        }
    }
}
