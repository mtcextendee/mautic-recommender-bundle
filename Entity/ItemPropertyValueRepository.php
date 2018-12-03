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

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mautic\CoreBundle\Entity\CommonRepository;

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

}
