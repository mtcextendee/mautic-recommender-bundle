<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventDateQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\ItemEventQueryBuilder;

class SegmentEventDateQueryBuilder extends ItemEventDateQueryBuilder
{
    /**
     * @return string
     */
    public function getIdentificator()
    {
        return 'lead_id';
    }

    /**
     * {@inheritdoc}
     */
    public static function getServiceId()
    {
        return 'mautic.recommender.query.builder.segment.event_date';
    }

    /**
     * @return string
     */
    public function getParentIdentificator()
    {
        return 'id';
    }
}
