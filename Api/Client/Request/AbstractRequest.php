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

use MauticPlugin\MauticRecommender\Exception\ItemIdNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRequest
{
    protected $options = [];
    protected $option = [];
    protected $repo;

    /**
     * @var ItemModel
     */
    private $itemModel;


    /**
     * ItemRequest constructor.
     *
     * @param array     $options
     * @param ItemModel $itemModel
     */
    public function __construct(array $options, ItemModel $itemModel)
    {
        $this->options = $options;
        $this->itemModel = $itemModel;
    }


    abstract protected function findExist();
    abstract protected function newEntity();


    /**
     * @param       $option
     *
     * @return bool
     */
    protected function add($option)
    {
        $item = $this->findExist();
        if ($item) {
            return false;
        }

        return $this->setValues($this->newEntity(), $option);
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
     *
     */
    public function execute()
    {
        $items = [];
        foreach ($this->getOptions() as $option) {
            $this->setOption($option);
            $add = $this->add($option);
            if ($add) {
                $items[] = $add;
            }
        }
        if (!empty($items)) {
            $this->getRepo()->saveEntities($items);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return ItemModel
     */
    public function getItemModel()
    {
        return $this->itemModel;
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

