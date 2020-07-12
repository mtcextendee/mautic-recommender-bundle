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

use MauticPlugin\MauticRecommenderBundle\Entity\EventLogValue;
use MauticPlugin\MauticRecommenderBundle\Entity\Property;

class AddItemValues extends AbstractRequest
{
    public function add()
    {
        $options = $this->getOptions();
        $item    = $options['item'];
        unset($options['item']);
        $i = 0;
        foreach ($options as $key => $option) {
            /** @var AddProperty $addProperty */
            $addProperty = $this->getClient()->send(
                'AddProperty',
                ['name' => $key]
            );
            /** @var Property $property */
            $property    = $addProperty->addIfNotExist();
            if (!$property->getId()) {
                print_r($option);
                $property->setType($this->getPropertyType($option));
                $addProperty->getRepo()->saveEntity($property);
            }
            $addItemValue = $this->getClient()->send(
                'AddItemValue',
                ['value' => $option,  'item'=> $item, 'property' => $property]
            );
            $value                   = $addItemValue->addOrEditIfExist();
            if ($value->isChanged('value')) {
                $this->addEntity($value);
            }
        }

        $entities = $addItemValue->getRepo()->findBy(['item'=> $item]);
        foreach ($entities as $entity) {
            if (!isset($this->getOptions()[$entity->getProperty()->getName()])) {
                $this->addDeleteEntity($entity);
            }
        }
    }

    /**
     * Just return new entity.
     *
     * @return EventLogValue
     */
    public function newEntity()
    {
        return new EventLogValue();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValueRepository
     */
    public function getRepo()
    {
        return $this->getModel()->getEventLogValueRepository();
    }
}
