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

use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommender\Exception\ItemIdNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyModel;

class Property
{
    use AddTrait;

    /** @var \MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyRepository */
    protected $repo;

    /**
     * @var ItemPropertyModel
     */
    protected $model;

    /** @var  array */
    protected $options;


    /**
     * Property constructor.
     *
     * @param array             $options
     * @param ItemPropertyModel $itemPropertyModel
     */
    public function __construct(array $options, ItemPropertyModel $itemPropertyModel)
    {
        $this->model   = $itemPropertyModel;
        $this->repo    = $itemPropertyModel->getRepository();
        $this->options = $options;
    }

    public function processPropertyFromItems($items)
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

