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

use MauticPlugin\MauticRecommenderBundle\Entity\ItemProperty;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue;

class AddItemPropertyValue extends AbstractRequest
{

    /**
     * Just return new entity
     *
     * @return ItemPropertyValue
     */
    public function newEntity()
    {
        return new ItemPropertyValue();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValueRepository
     */
    public function getRepo()
    {
        return $this->getModel()->getItemPropertyValueRepository();
    }

    /**
     * @param null  $entity
     * @param array $options
     */
    public function setValues($entity = null, array $options)
    {
        $item = $this->getModel()->getRepository()->findOneBy(['itemId' => $options['itemId']]);
        if (!$item) {
            return;
        }
        $currentEntities = $this->getRepo()->findBy(['item' => $item]);
        $items           = [];
        $deleteItems     = [];
        foreach ($options as $propertyName => $value) {
            if(is_array($value))
            {
                continue;
            }
            $property = $this->findPropertyByName($propertyName);
            if ($property === null) {
                continue;
            }
            /** @var ItemPropertyValue $itemPropertyValue */
            $itemPropertyValue = $this->getRepo()->findOneBy(['item' => $item, 'property' => $property]);
            if (!$itemPropertyValue) {
                $entity = $this->newEntity();
                $entity->setValues($item, $property, $value);
                $items[] = $entity;
            } elseif ($value !== $itemPropertyValue->getValue()) {
                $itemPropertyValue->setValue($value);
                $items[] = $itemPropertyValue;
            }
            /** @var ItemPropertyValue $currentEntity */
            if ($currentEntities) {
                foreach ($currentEntities as $currentEntity) {
                    if (!isset($options[$currentEntity->getProperty()->getName()])) {
                        $deleteItems[] = $currentEntity;
                    }
                }
            }
        }
        $this->getRepo()->deleteEntities($deleteItems);

        return $items;
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
            $properties = $this->getModel()->getItemPropertyRepository()->findAll();
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

