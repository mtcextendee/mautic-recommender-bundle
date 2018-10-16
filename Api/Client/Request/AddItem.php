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

class AddItem extends Item
{
    protected function add($option)
    {
        $item = $this->repo->findOneBy(['itemId' => $option['itemId']]);
        if ($item) {
            return false;
        }

        return $this->model->setValues(null, $option);
    }

}

