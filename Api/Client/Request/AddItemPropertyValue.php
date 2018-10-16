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
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyValueModel;

class AddItemPropertyValue extends PropertyValue
{

    protected function add($option)
    {
        // not update If already exist
        return $this->model->setValues(null, $option);
    }

    public function get($option)
    {
        $items =  $this->repo->getEntities(
            [
                'filter'         => [
                    'force' => [
                        [
                            'column' => 'item_id',
                            'expr'   => 'eq',
                            'value'  => $option['itemId'],
                        ],
                    ],
                ],
                'hydration_mode' => 'HYDRATE_ARRAY',
            ]
        );
    }

    public function execute()
    {
        foreach ($this->options as $option) {
            $items = $this->get($option);
            $entities = [];
            foreach ($option as $opt) {
                $entities[] = $this->add($option);
            }
            $this->repo->saveEntities($entities);
        }

    }
}

