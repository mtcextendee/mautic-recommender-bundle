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
use MauticPlugin\MauticRecommenderBundle\Model\EventLogModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRequest
{
    protected $options = [];
    protected $option = [];
    protected $repo;

    /**
     * @var
     */
    private $model;


    /**
     * ItemRequest constructor.
     *
     * @param array $options
     * @param $model
     */
    public function __construct(array $options, $model)
    {
        $this->options = $options;
        $this->model   = $model;
    }


    /**
     * @return bool
     */
    protected function findExist(){
        return false;
    }

    protected function newEntity(){ }

    /**
     * @param bool $save
     *
     * @return array
     */
    public function execute($save = true)
    {
        $items = [];
        foreach ($this->getOptions() as $option) {
            $this->setOption($option);
            $add = $this->add();
            if (is_array($add)) {
                $items = array_merge($items, $add);
            }else{
                $items[] = $add;
            }
        }
        $items = array_filter($items);
        if (!empty($items)) {
            if ($save) {
                $this->getRepo()->saveEntities($items);
            }else{
                return array_filter($items);
            }
        }
    }

    /**
     * @return bool
     */
    protected function add()
    {
        $item = $this->findExist();
        if ($item) {
            return false;
        }
        return $this->setValues($this->newEntity(), $this->getOption());
    }


    /**
     * @param null $entity
     * @param array     $options
     *
     * @return Item
     */
    public function setValues($entity = null, array $options)
    {
        if ($entity === null) {
            return;
        }

        $accessor = new PropertyAccessor();
        foreach ($options as $key=>$value){
            try {
                $accessor->setValue($entity, $key, $value);
            } catch (\Exception $exception) {

            }
        }

        return $entity;
    }

    /**
     * @return array
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
     * @return mixed
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return ItemModel|EventLogModel
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
     * @param array $option
     */
    public function setOption(array $option)
    {
        $this->option = $option;
    }

}

