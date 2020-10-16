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
use MauticPlugin\MauticRecommenderRecombeeBundle\Recombee\Sync\DAO\InputDAO;

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

    /**
     * @return array
     */
    public function getItemsToSync(InputDAO $inputDAO)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('ri.id, ri.item_id')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item', 'ri')
            ->where($qb->expr()->gt('ri.date_modified', ':dateModified'))
            ->orderBy('ri.date_modified', 'ASC')
            ->setMaxResults($inputDAO->getLimit())
            ->setParameter('dateModified', $inputDAO->getStartDateTime()->format('Y-m-d H:i:s'));

        return $qb->execute()->fetchAll();
    }

    /**
     * @return array
     */
    public function findAllArray()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from('MauticRecommenderBundle:Item', 'i')
            ->select('i')
            ->getQuery()->getArrayResult();
    }

    /**
     * @param array $itemIds
     *
     * @return array
     */
    public function findActiveExcluding($itemIds)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select('id, item_id')
            ->from(MAUTIC_TABLE_PREFIX.'recommender_item', 'i')
            ->andWhere($qb->expr()->eq('i.active', '1'));
        if (!empty($itemIds)) {
            foreach ($itemIds as &$item) {
                $item = $qb->expr()->literal($item);
            }
            $qb->andWhere($qb->expr()->notIn('i.item_id', $itemIds));
        }

        return $qb->execute()->fetchAll();
    }

    /**
     * @return array
     */
    public function getEventNamesAsChoices()
    {
        $events = $this->findAllArray();

        return array_combine(array_column($events, 'id'), array_column($events, 'itemId'));
    }
}
