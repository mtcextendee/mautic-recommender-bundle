<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator;

use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\ContactSegmentFilterOperator;
use Mautic\LeadBundle\Segment\Decorator\CustomMappedDecorator;
use Mautic\LeadBundle\Services\ContactSegmentFilterDictionary;

class Decorator extends CustomMappedDecorator
{
    /**
     * Decorator constructor.
     */
    public function __construct(
        ContactSegmentFilterOperator $contactSegmentFilterOperator,
        ContactSegmentFilterDictionary $contactSegmentFilterDictionary,
        SegmentDictionary $dictionary
    ) {
        parent::__construct($contactSegmentFilterOperator, $contactSegmentFilterDictionary);
        $this->dictionary = $dictionary->getDictionary();
    }

    /**
     * @return array|bool|float|string|null
     */
    public function getParameterValue(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        if ($contactSegmentFilterCrate->isDateType()) {
            return $contactSegmentFilterCrate->getFilter();
        }

        return parent::getParameterValue($contactSegmentFilterCrate);
    }
}
