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

use Mautic\CampaignBundle\Model\EventModel;
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

    /** @var array  */
    private $entities = [];


    /** @var array  */
    private $deleteEntities = [];


    /**
     * ItemRequest constructor.
     *
     * @param Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->options = $this->client->getOptions();
        $this->optionsResolver =  $this->client->getOptionsResolver();
        $this->model   = $this->client->getClientModel();

    }

    protected function newEntity(){}


    protected function getAll()
    {
        return $this->getRepo()->findAll();
    }

    protected function find()
    {
        return false;
    }

    public function run()
    {

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


    public function getEntity()
    {
         return $this->getEntities()[0];
    }

    public function save()
    {
        if (count($this->getEntities()) == 1) {
            return $this->getRepo()->saveEntity($this->getEntities()[0]);
        }elseif(count($this->getEntities()) > 1){
            return $this->getRepo()->saveEntities($this->getEntities());
        }
    }


    public function delete()
    {
        if (count($this->getDeleteEntities()) == 1) {
            return $this->getRepo()->deleteEntity($this->getDeleteEntities()[0]);
        }elseif(count($this->getDeleteEntities()) > 1){
            return $this->getRepo()->deleteEntities($this->getDeleteEntities());
        }
    }

    /**
     * @param bool $save
     *
     * @return array
     */
    public function execute($save = true)
    {
        $items = [];
        if (!empty($items)) {
            if ($save) {
                $this->getRepo()->saveEntities($items);

            }else{
                return array_filter($items);
            }
        }
    }


    /**
     * @param object $entity
     *
     * @return object
     */
    public function setValues($entity)
    {
        $accessor = new PropertyAccessor();
        foreach ($this->getOptions() as $key=>$value){
            try {
                $accessor->setValue($entity, $key, $value);
            } catch (\Exception $exception) {

            }
        }
        return $entity;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {

        return $this->options;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addOption($key, $value)
    {
        $this->option[$key] = $value;
    }

    /**
     * @param $key
     */
    public function removeOption($key)
    {
        if (isset($this->option[$key])) {
            unset($this->option[$key]);
        }
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
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getEntities(): array
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
     * @param array $entities
     */
    public function setEntities(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getDeleteEntities(): array
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
     * @param array $deleteEntities
     */
    public function setDeleteEntities(array $deleteEntities)
    {
        $this->deleteEntities = $deleteEntities;
    }

    /**
     * @return Options
     */
    public function getOptionsResolver(): Options
    {
        return $this->optionsResolver;
    }

    /**
     * Return property type
     *
     * @param $property
     *
     * @return string
     */
    protected function getPropertyType($property)
    {
        if (is_array($property)) {
            return 'set';
        } elseif (is_int($property)) {
            return 'int';
        } elseif (is_double($property)) {
            return 'float';
        } elseif (is_bool($property)) {
            return 'boolean';
        } elseif ($this->isDateTime($property)) {
            return'datetime';
        } else {
            return 'string';
        }
    }

    /**
     * @param $date
     *
     * @return bool
     */
    private function isDateTime($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d g:i:s', $date);
        $d2 = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if(($d && $d->format('Y-m-d g:i:s') == $date) || ($d2 && $d2->format('Y-m-d H:i:s') == $date))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

