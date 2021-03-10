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

use Doctrine\DBAL\Query\QueryBuilder;
use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;

class RecommenderFilterQueryBuilder extends BaseFilterQueryBuilder
{
    public function setParameters(QueryBuilder $queryBuilder, $parameters, $filterParameters, ContactSegmentFilter $filter)
    {
        $type = 'string';
        if (isset($filter->contactSegmentFilterCrate->getArray()['type'])) {
            $type = $filter->contactSegmentFilterCrate->getArray()['type'];
        }
        if (!is_array($parameters)) {
            $type = $this->transformType($type, $filterParameters);

            switch ($filter->getOperator()) {
                case 'regexp':
                case 'notRegexp':
                $filterParameters = str_replace('\|', '|', preg_quote($filterParameters));
                    break;
            }

            return $queryBuilder->setParameter($parameters, $filterParameters, $type);
        }
        foreach ($parameters as $parameter) {
            $parameterValue = array_shift($filterParameters);
            $type           = $this->transformType($type, $parameterValue);
            $queryBuilder->setParameter($parameter, $parameterValue, $type);
        }
    }

    /**
     * @param $type
     *
     * @return string
     */
    private function transformType($type, &$parameter)
    {
        switch ($type) {
            case 'select':
            case 'multiselect':
                return 'string';
            case 'bool':
                $parameter = (bool) $parameter;

                return 'boolean';
            case 'int':
            case 'number':
                $parameter = (int) $parameter;

                return 'integer';
            case 'date':
                $dateTimeHelper = new DateTimeHelper($parameter);
                $parameter      = $dateTimeHelper->getUtcDateTime();

                return 'datetime';
            default:
                return  $type;
        }
    }
}
