<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Enum;

use Mautic\CoreBundle\Helper\ArrayHelper;

class PropertyTypeEnum
{
    const PRICE    = 'price';
    const NAME    = 'product';

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::DETAIL_VIEW,
            self::CART_ADDITIONS,
            self::CART_REMOVE,
            self::PURCHASE,
        ];
    }

    /**
     * @return array
     */
    public static function getChoices()
    {
        return [
            self::DETAIL_VIEW    => 'mautic.recommender.event.type.detail_view',
            self::CART_ADDITIONS => 'mautic.recommender.event.type.cart_additions',
            self::CART_REMOVE => 'mautic.recommender.event.type.cart_remove',
            self::PURCHASE       => 'mautic.recommender.event.type.purchase',
        ];
    }

    /**
     * @param string|null $type
     *
     * @return mixed
     */
    public static function getChoice($type = null)
    {
        return ArrayHelper::getValue($type, self::getChoices());
    }
}
