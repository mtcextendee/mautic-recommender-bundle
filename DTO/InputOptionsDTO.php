<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\DTO;


class InputOptionsDTO
{
   public function __construct(array $options)
   {
        $this->forceValue = $options['force-value'] ?? null;
   }
}

