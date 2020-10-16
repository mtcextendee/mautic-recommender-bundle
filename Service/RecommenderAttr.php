<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Service;

use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;

class RecommenderAttr
{
    private $recommenderAttr = ['type', 'itemId', 'userId', 'limit'];

    public function getRecommenderAttr(): array
    {
        return $this->recommenderAttr;
    }
}
