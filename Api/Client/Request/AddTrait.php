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


trait AddTrait
{
    public function execute()
    {
        $items = [];
        foreach ($this->options[0] as $option) {
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

