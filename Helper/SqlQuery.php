<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Helper;

use MauticPlugin\MauticCrmBundle\Integration\Salesforce\QueryBuilder;

class SqlQuery
{
    public static $query = [];

    /**
     * @param QueryBuilder $query
     *
     * @return string
     */
    public static function getQuery($query)
    {
        $q                  = $query->getSQL();
        $params             = $query->getParameters();

        foreach ($params as $name => $param) {
            if (is_array($param)) {
                $param = implode(',', $param);
            } elseif ($param instanceof \DateTimeInterface) {
                $param = $param->format('Y-m-d');
            }
            $q = str_replace(":$name", "'$param'", $q);
        }

        return $q;
    }

    /**
     * @param QueryBuilder $query
     */
    public static function debugQuery($query)
    {
        self::$query[] = self::getQuery($query);
    }
}
