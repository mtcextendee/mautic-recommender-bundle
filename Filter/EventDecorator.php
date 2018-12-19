<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter;


use Mautic\LeadBundle\Segment\Decorator\CustomMappedDecorator;

class EventDecorator extends CustomMappedDecorator
{
    /**
     * @param ContactSegmentFilterDictionary $dictionary
     */
    public function setDictionary($dictionary)
    {
        $this->dictionary = $dictionary;
    }


}
