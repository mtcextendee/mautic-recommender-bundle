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

class AddEventLogPropertyValues extends AbstractRequest
{
    public function add()
    {
        $options  = $this->getOptions();
        $eventLog = $options['eventLog'];
        unset($options['eventLog']);
        unset($options['contactId']);
        foreach ($options as $key => $option) {
            /** @var AddProperty $addProperty */
            $addProperty = $this->getClient()->send(
                'AddProperty',
                ['name' => $key, 'type' => $this->getPropertyType($option)]
            );
            $property    = $addProperty->addIfNotExist();
            if (!$property->getId()) {
                $addProperty->save();
            }
            /** @var AddEventLogPropertyValue $addEventLogPropertyValue */
            $addEventLogPropertyValue = $this->getClient()->send(
                'AddEventLogPropertyValue',
                ['value' => $option,  'eventLog'=> $eventLog, 'property' => $property]
            );
            $value                   = $addEventLogPropertyValue->add();
            $this->addEntity($value);
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
