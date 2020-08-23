<?php
/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query;

use MauticPlugin\MauticRecommenderBundle\Filter\Recommender\Query\AbandonedCartQueryBuilder;

class SegmentAbandonedCartQueryBuilder extends AbandonedCartQueryBuilder
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
        return 'mautic.recommender.query.builder.abandoned_cart';
    }

}
