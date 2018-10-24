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

class AddEventLogPropertyValue extends AbstractRequest
{
    /**
     * Just return new entity
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

    /**
     * @param null  $entity
     * @param array $options
     */
    public function setValues($entity = null, array $options)
    {
        /** @var AddProperty $addProperty */
        $addProperty = $this->getClient()->send('AddProperty', $options);
        foreach ($options as $propertyName => $value) {
            if(is_array($value) || is_object($value))
            {
                continue;
            }

            $property = $addProperty->findPropertyByName($propertyName);
            if (!$property) {
                continue;
            }
                $entity = $this->newEntity();
                $entity->setValues($options['eventLog'], $property, $value);
                $items[] = $entity;

        }

        return $items;
    }
}

