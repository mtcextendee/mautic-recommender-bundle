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

class FiltersEnum
{
    const ABANDONED_CART      = 'abandoned_cart';

    const CUSTOM              = 'custom';

    const BEST_SELLERS        = 'best_sellers';

    const POPULAR_PRODUCTS    = 'popular_products';

    const RECENTLY_CREATED    = 'recently_created';

    public static function getFilterTarget(string $filterTarget = null): ?string
    {
        return self::getFilterTargets()[$filterTarget] ?? null;
    }

    public static function getFilterTargets(): array
    {
        return [
            self::BEST_SELLERS     => 'recommender.form.best_sellers',
            self::POPULAR_PRODUCTS => 'recommender.form.popular_products',
            self::ABANDONED_CART   => 'recommender.form.event.abandoned_cart',
            self::RECENTLY_CREATED => 'recommender.form.event.recently_created',
            self::CUSTOM           => 'recommender.form.event.custom',
        ];
    }
}
