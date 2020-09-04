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

class EventLogValueRepository extends CommonRepository
{
    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'elv';
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('DISTINCT v.property_id as id, p.name, p.type')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_event_log_property_value', 'v');
        $qb->join('v', MAUTIC_TABLE_PREFIX.'recommender_property', 'p', 'v.property_id = p.id');
        $qb->where($qb->expr()->eq('p.segment_filter', true));

        return $qb->execute()->fetchAll();
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function findMostActiveContacts($limit = 25)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('el.lead_id')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', 'el')
            ->groupBy('el.lead_id')
            ->orderBy('COUNT(el.id)', ' desc')
            ->setMaxResults($limit);

        return $qb->execute()->fetchAll();
    }
}
