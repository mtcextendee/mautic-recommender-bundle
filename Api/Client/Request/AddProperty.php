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

use MauticPlugin\MauticRecommenderBundle\Entity\Property;

class AddProperty extends AbstractRequest
{
    /**
     * Find exist entity
     *
     * @return bool|object
     */
    public function findExist()
    {
        return $this->getRepo()->findOneBy(['name' => $this->getOption()['name']]);
    }

    /**
     * Just return new entity
     *
     * @return Property
     */
    public function newEntity()
    {
        return new Property();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\PropertyRepository
     */
    public function getRepo()
    {
        return $this->getModel()->getPropertyRepository();
    }

    /**
     * @return array
     */
    public function getOptions(){
        return $this->parseProperties($this->options);
    }

    /**
     * Find ItemProperty entity by name
     *
     * @param $name
     *
     * @return null
     */
    public function findPropertyByName($name)
    {
        static $properties;

        if (!isset($properties)) {
            $properties = $this->getModel()->getPropertyRepository()->findAll();
        }
        /** @var Property $propertyEntity */
        foreach ($properties as $propertyEntity) {
            if ($propertyEntity->getName() === $name) {
                return $propertyEntity;
            }
        }

        return false;
    }

    /**
     * Process option data and select just properties (color=orange etc)
     *
     * @param $items
     *
     * @return array
     */
    private function parseProperties($items)
    {

        $uniqueParams = [];

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if (is_object($value)) {
                    continue;
                }else if (is_array($value)) {
                    if (count($value) == count($value, COUNT_RECURSIVE)) {
                        $item[$key] = json_encode(array_values($value));
                        unset($item[$key]);
                    } else {
                        unset($item[$key]);
                        continue;
                    }
                }
                if (!isset($uniqueParams[$key]) || $uniqueParams[$key] != '') {
                    $uniqueParams[$key] = $value;
                }
            }
        }
        $properties             = [];
        foreach ($uniqueParams as $key => $value) {
            $property = [];
            $property['name'] = $key;
            if (is_array($value)) {
                $property['type'] = 'set';
            } elseif (is_int($value)) {
                $property['type'] = 'int';
            } elseif (is_double($value)) {
                $property['type'] = 'float';
            } elseif (is_bool($value)) {
                $property['type'] = 'boolean';
            } elseif ($this->isDateTime($value)) {
                $property['type'] = 'datetime';
            } else {
                $property['type'] = 'string';
            }
            $properties[] = $property;
        }
        return $properties;
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

