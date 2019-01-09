<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Query;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Expression\CompositeExpression;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;

trait QueryBuilderTrait
{
    public function setParameters(QueryBuilder $queryBuilder, $parameters, $filterParameters, $type)
    {
        if (!is_array($parameters)) {
            return $queryBuilder->setParameter($parameters, $filterParameters, $type);
        }
        foreach ($parameters as $parameter) {
            $parameterValue = array_shift($filterParameters);
            $queryBuilder->setParameter($parameter, $parameterValue, $type);
        }
    }

}
