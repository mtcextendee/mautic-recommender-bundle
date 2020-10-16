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
     * @param null $filter
     *
     * @return array
     */
    public function getValuesForProperty(int $propertyId, int $limit = 1000, $filter = null)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('i.item_id, v.value')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item_property_value', 'v');
        $qb->leftJoin('v', MAUTIC_TABLE_PREFIX.'recommender_item', 'i', 'i.id = v.item_id');
        $qb->andWhere($qb->expr()->eq('v.property_id', $propertyId));
        $qb->andWhere($qb->expr()->eq('i.active', 1));

        if ($filter) {
            $qb->andWhere($qb->expr()->like('v.value', ':filter'))
            ->setParameter('filter', '%'.$filter.'%');
        }
        $qb->setMaxResults($limit);

        return  $qb->execute()->fetchAll();
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
