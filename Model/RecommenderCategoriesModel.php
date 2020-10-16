<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Model;

use Mautic\CoreBundle\Model\AbstractCommonModel;
use Mautic\CoreBundle\Model\AjaxLookupModelInterface;

class RecommenderCategoriesModel extends AbstractCommonModel implements AjaxLookupModelInterface
{
    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\PropertyRepository
     */
    public function getPropertyRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Property');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValueRepository
     */
    public function getItemPropertyValueRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:ItemPropertyValue');
    }

    /**
     * @param        $type
     * @param string $filter
     * @param int    $limit
     * @param int    $start
     * @param array  $options
     */
    public function getLookupResults($type, $filter = '', $limit = 100, $start = 0, $options = [])
    {
        $results = [];
        if ($property = $this->getPropertyRepository()->findOneBy(['name' => 'category'])) {
            $items = $this->getItemPropertyValueRepository()->getValuesForProperty(
                $property->getId(),
                200,
                !is_array($filter) ? $filter : ''
            );
            foreach ($items as $item) {
                $results[$item['value']] = $item['value'];
            }
        }

        return $results;
    }
}
