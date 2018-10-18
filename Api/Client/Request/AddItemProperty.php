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

class AddItemProperty extends AbstractRequest
{
    /**
     * Find exist entity
     *
     * @return null|object
     */
    public function findExist()
    {
        return $this->getRepo()->findOneBy(['name' => $this->getOption()['name']]);
    }

    /**
     * Just return new entity
     *
     * @return Item
     */
    public function newEntity()
    {
        return new ItemProperty();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyRepository
     */
    public function getRepo()
    {
        return $this->getItemModel()->getItemPropertyRepository();
    }
    /**
     * @return array
     */
    public function getOptions(){
        return $this->parseProperties($this->options);
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
                if (is_array($value)) {
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

