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
}
