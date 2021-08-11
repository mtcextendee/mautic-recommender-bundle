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

/**
 * Class EventRepository.
 */
class EventRepository extends CommonRepository
{
    public function findAllArray()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from('MauticRecommenderBundle:Event', 'p')
            ->select('p')
            ->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getEventNamesAsChoices()
    {
        $events = $this->findAllArray();

        return array_combine(array_column($events, 'id'), array_column($events, 'name'));
    }

    /**
     * @param null $eventId
     *
     * @return bool|string
     */
    public function getEventsCount($eventId = null)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('COUNT(el.event_id)')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', 'el');

        if ($eventId) {
            $qb->andWhere($qb->expr()->eq('el.event_id', ':event_id'))
                ->setParameter('event_id', $eventId);
        }

        return $qb->execute()->fetchColumn(0);
    }

    /**
     * @param null $eventId
     *
     * @return bool|string
     */
    public function getEventLastDate($eventId = null)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('MAX(el.date_added)')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', 'el');

        if ($eventId) {
            $qb->andWhere($qb->expr()->eq('el.event_id', ':event_id'))
                ->setParameter('event_id', $eventId);
        }

        return $qb->execute()->fetchColumn(0);
    }
}
