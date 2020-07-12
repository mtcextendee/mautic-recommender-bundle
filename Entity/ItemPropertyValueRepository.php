<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use MauticPlugin\MauticRecommenderBundle\Helper;

class ItemPropertyValueRepository extends CommonRepository
{
    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'ripv';
    }

    /**
     * @return array
     */
    public function getItemValueProperties()
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('DISTINCT v.property_id as id, p.name, p.type')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item_property_value', 'v');
        $qb->join('v', MAUTIC_TABLE_PREFIX.'recommender_property', 'p', 'v.property_id = p.id');
        $qb->where($qb->expr()->eq('p.segment_filter', true));
        return $qb->execute()->fetchAll();
    }


    /**
     * @param int $propertyId
     *
     * @return array
     */
    public function getValuesForProperty(int $propertyId)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('v.item_id, v.value')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item_property_value', 'v');
        $qb->where($qb->expr()->eq('v.property_id', $propertyId));
        if ($values = $qb->execute()->fetchAll()) {
            return array_combine(array_column($values, 'item_id'), array_column($values, 'value'));
        }

        return [];
    }

    /**
     * @param null $itemId
     *
     * @return array
     */
    public function getValues($itemId = null)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('p.id, p.name, pv.value, p.type')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item', 'i')
            ->join('i', MAUTIC_TABLE_PREFIX.'recommender_item_property_value', 'pv', 'pv.item_id = i.id')
            ->join('pv', MAUTIC_TABLE_PREFIX.'recommender_property', 'p', 'pv.property_id = p.id')
            ->where($qb->expr()->eq('i.id', ':itemId'))
            ->setParameter('itemId', $itemId);
        return $qb->execute()->fetchAll();
    }
}
