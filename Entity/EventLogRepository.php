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
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\TimelineTrait;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;

/**
 * Class EventLogRepository.
 */
class EventLogRepository extends CommonRepository
{
    use TimelineTrait;

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
            ->orderBy('COUNT(el.id)', ' desc')
            ->setMaxResults($limit);

        return $qb->execute()->fetchAll();
    }

    /**
     * @return array
     */
    public function getTimeLineEvents(Lead $contact, array $options = [])
    {
        $alias = $this->getTableAlias();
        $qb    = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select($alias.'.*')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_event_log', $alias);

        if ($contact) {
            $qb->andWhere($alias.'.lead_id = :lead')
                ->setParameter('lead', $contact->getId());
        }

        if (!empty($options['search'])) {
            $qb->innerJoin($alias, 'recommender_event', 're', 're.id = '.$alias.'.event_id');
            $qb->innerJoin($alias, 'recommender_item', 'ri', 'ri.id = '.$alias.'.item_id');
            $qb->leftJoin('ri', 'recommender_item_property_value', 'ripv', 'ri.id = ripv.item_id');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('re.name', $qb->expr()->literal('%'.$options['search'].'%')),
                    $qb->expr()->like('ripv.value', $qb->expr()->literal('%'.$options['search'].'%'))
                )
            );
            $qb->groupBy($alias.'.id');
        }

        return $this->getTimelineResults($qb, $options, 're.name', $alias.'.date_added', [], ['date_added']);
    }
}
