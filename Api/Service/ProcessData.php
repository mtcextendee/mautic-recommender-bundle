<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Service;

use Mautic\CoreBundle\Helper\InputHelper;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;

class ProcessData
{
    private $requestsPropertyName = [];

    private $requestsPropertyValues = [];

    /**
     * @param $items
     *
     * @return array
     */
    public function itemsToMultiArray($items)
    {
        if (is_array(end($items))) {
            return $items;
        } else {
            return [$items];
        }
    }

    /**
     * ProcessData constructor.
     *
     * @param array $items
     * @param       $funcProperty
     * @param       $funcValue
     */
    public function __construct(array $items, $funcProperty, $funcValue)
    {
        // Prevent attributes with diacritics
        foreach ($items as $key=>$item) {
            foreach ($item as $key2 => $value) {
                if ($newKey2 = InputHelper::transliterate(trim($key2))) {
                    if ($key2 != $newKey2) {
                        $items[$key][$newKey2] = $value;
                        unset($items[$key][$key2]);
                    }
                }
            }
        }

        $funcProperty = 'Recommender\RecommApi\Requests\\'.$funcProperty;
        $funcValue    = 'Recommender\RecommApi\Requests\\'.$funcValue;
        $items        = $this->itemsToMultiArray($items);
        $uniqueParams = [];

        foreach ($items as $item) {
            /** @todo  check */
            if (!isset($item['id'])) {
                throw new \Exception('ID  missing: '.print_r($item, true));
            }
            $itemId =  $item['id'];
            $item['item-id'] = $itemId;
            unset($item['id']);
            foreach ($item as $key => $value) {
                if (is_array($value)) {
                    if (count($value) == count($value, COUNT_RECURSIVE)) {
                        $item[$key] = json_encode(array_values($value));
                        unset($item[$key]);
                    } elseif($key == 'images' && is_array($value)) {
                        $value = array_shift(array_slice($value, 0, 1));
                        if (isset($value['src'])) {
                            $item[$key] = $value['src'];
                        } else {
                            continue;
                        }
                    } else {
                        unset($item[$key]);
                        continue;
                    }
                }
                if (!isset($uniqueParams[$key]) || $uniqueParams[$key] != '') {
                    $uniqueParams[$key] = $value;
                }
                // convert date to timestamp
                if ($this->isDateTime($value)) {
                    $item[$key] = strtotime($value);
                }
            }
            $this->requestsPropertyValues[] = new $funcValue($itemId, $item, ['cascadeCreate' => true]);
        }

        $allowedImagesFileTypes = ['gif', 'png', 'jpg'];
        foreach ($uniqueParams as $key => $value) {
            if (is_array($value)) {
                $this->requestsPropertyName[] = new $funcProperty($key, 'set');
            } else {
                if (in_array(pathinfo($value, PATHINFO_EXTENSION), $allowedImagesFileTypes)) {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'image');
                } elseif (is_int($value)) {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'int');
                } elseif (is_double($value)) {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'double');
                } elseif (is_double($value)) {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'double');
                } elseif (is_bool($value)) {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'boolean');
                } elseif ($this->isDateTime($value)){
                    $this->requestsPropertyName[] = new $funcProperty($key, 'timestamp');
                } else {
                    $this->requestsPropertyName[] = new $funcProperty($key, 'string');
                }
            }
        }
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

    /**
     * @return array
     */
    public function getRequestsPropertyName()
    {
        return $this->requestsPropertyName;
    }

    /**
     * @return array
     */
    public function getRequestsPropertyValues()
    {
        return $this->requestsPropertyValues;
    }
}

