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
use MauticPlugin\MauticRecommenderBundle\Entity\ItemProperty;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyValueModel;

class AddItemPropertyValue extends PropertyValue
{

    protected function add($option)
    {
        $entitiesToSave = [];
        $item           = $this->model->getItemRepository()->findOneBy(['itemId' => $option['itemId']]);
        if (!$item) {
            return;
        }
        foreach ($option as $propertyName => $value) {
            $property = $this->findPropertyByName($propertyName);
            if ($property === null) {
                continue;
            }
            /** @var ItemPropertyValue $itemPropertyValue */
            $itemPropertyValue = $this->repo->findOneBy(['item' => $item, 'property' => $property]);
        if (!$itemPropertyValue) {
            $data             = [];
            $data['item']     = $item;
            $data['property'] = $property;
            $data['value']    = $value;
            $entitiesToSave[] = $this->model->setValues(null, $data);
        } elseif ($value != $itemPropertyValue->getValue()) {
            $itemPropertyValue->setValue($value);
            $entitiesToSave[] = $itemPropertyValue;
        }
    }

return $entitiesToSave;
    }


    public function execute()
    {
        $items = [];
        foreach ($this->options as $option) {
            $add = $this->add($option);
            if ($add) {
                $items[] = $add;
            }
            $this->deleteEntities($option);
        }
        if (!empty($items)) {
            foreach ($items as $item) {
                $this->repo->saveEntities($item);
            }
        }
    }

    /**
     * @param $option
     *
     * @return array
     */
    private function deleteEntities($option)
    {
        $item           = $this->model->getItemRepository()->findOneBy(['itemId' => $option['itemId']]);
        if (!$item) {
            return;
        }
        $entities = $items = $this->repo->findBy(['item'=>$item]);
        $deleteEntities = [];
        /** @var ItemPropertyValue $entity */
        foreach ($entities as $entity) {
            if (!isset($option[$entity->getProperty()->getName()])) {
                $deleteEntities[] = $entity;
            }
        }
        $this->repo->deleteEntities($deleteEntities);
    }


    /**
     * Find ItemProperty entity by name
     *
     * @param $name
     *
     * @return null
     */
    private function findPropertyByName($name)
    {
        static $properties;

        if (!isset($properties)) {
            $properties = $this->model->getItemPropertyRepository()->findAll();
        }
        /** @var ItemProperty $propertyEntity */
        foreach ($properties as $propertyEntity) {
            if ($propertyEntity->getName() === $name) {
                return $propertyEntity;
            }
        }

        return null;
    }
}

