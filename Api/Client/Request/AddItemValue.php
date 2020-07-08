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

use MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue;
use MauticPlugin\MauticRecommenderBundle\Entity\Property;

class AddItemValue extends AbstractRequest
{
    public function find()
    {
        return $this->getRepo()->findOneBy(['item' => $this->getOptionsResolver()->getOption('item'), 'property'=> $this->getOptionsResolver()->getOption('property')]);
    }

    /**
     * Just return new entity.
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

    // /**
    //  * @param null  $entity
    //  * @param array $options
    //  */
    // public function setValues($entity = null, array $options)
    // {
    //     $item = $this->getModel()->getRepository()->findOneBy(['itemId' => $options['itemId']]);
    //     if (!$item) {
    //         return;
    //     }
    //     $currentEntities = $this->getRepo()->findBy(['item' => $item]);
    //     $items           = [];
    //     $deleteItems     = [];
    //
    //     // All properties check
    //     /** @var AddProperty $addProperty */
    //     $addProperty = $this->getClient()->send('AddProperty', $options);
    //     foreach ($options as $propertyName => $value) {
    //         if(is_array($value) || is_object($value))
    //         {
    //             continue;
    //         }
    //
    //         $property = $addProperty->findPropertyByName($propertyName);
    //         if (!$property) {
    //             continue;
    //         }
    //         /** @var ItemPropertyValue $itemPropertyValue */
    //         $itemPropertyValue = $this->getRepo()->findOneBy(['item' => $item, 'property' => $property]);
    //         if (!$itemPropertyValue) {
    //             $entity = $this->newEntity();
    //             $entity->setValues($item, $property, $value);
    //             $items[] = $entity;
    //         } elseif ($value !== $itemPropertyValue->getValue()) {
    //             $itemPropertyValue->setValue($value);
    //             $items[] = $itemPropertyValue;
    //         }
    //         /** @var ItemPropertyValue $currentEntity */
    //         if ($currentEntities) {
    //             foreach ($currentEntities as $currentEntity) {
    //                 if (!isset($options[$currentEntity->getProperty()->getName()])) {
    //                     $deleteItems[] = $currentEntity;
    //                 }
    //             }
    //         }
    //     }
    //     $this->getRepo()->deleteEntities($deleteItems);
    //
    //     return $items;
    // }
}
