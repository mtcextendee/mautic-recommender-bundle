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
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;

/**
 * Class EventLogRepository
 * @package MauticPlugin\MauticRecommenderBundle\Entity
 */
class EventLogRepository extends CommonRepository
{

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'el';
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
            ->orderBy('COUNT(el.id)',' desc')
            ->setMaxResults($limit);
        return $qb->execute()->fetchAll();
    }

}
