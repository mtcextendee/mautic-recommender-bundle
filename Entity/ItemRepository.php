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
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ItemRepository extends CommonRepository
{
    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'ri';
    }

    /**
     * @param null $contactId
     * @param int  $max
     *
     * @return array
     */
    public function getContactsItemsByPoints($contactId = null, $max = 10)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('DISTINCT ri.id as id, SUM(e.weight) as totalWeight')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item', 'ri')
            ->join('ri', MAUTIC_TABLE_PREFIX.'recommender_event_log', 'el', 'el.item_id = ri.id')
            ->join('el', MAUTIC_TABLE_PREFIX.'recommender_event', 'e', 'el.event_id = e.id')
            ->where($qb->expr()->eq('el.lead_id', ':contactId'))
            ->orderBy('SUM(e.weight)', 'DESC')
            ->groupBy('ri.id')
            ->setMaxResults($max)
            ->setParameter('contactId', $contactId);
        return $qb->execute()->fetchAll();
    }

}
