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
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyModel;

class AddItemProperty extends Property
{
    protected function add($option)
    {
        // not update If already exist
        $property = $this->repo->findOneBy(['name' => $option['name']]);
        if ($property) {
            return false;
        }

        return $this->model->setValues(null, $option);
    }

    public function execute()
    {
        $options = $this->processPropertyFromItems($this->options[0]);
        $items = [];
        foreach ($options as $option) {
            $add = $this->add($option);
            if ($add) {
                $items[] = $add;
            }
        }
        if (!empty($items)) {
            $this->repo->saveEntities($items);
        }
    }
}

