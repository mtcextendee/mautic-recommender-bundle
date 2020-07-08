<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Client\Request;

use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Entity\EventLog;
use MauticPlugin\MauticRecommenderBundle\Entity\EventLogValue;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue;

class AddEventLog extends AbstractRequest
{
    /**
     * Just return new entity.
     *
     * @return EventLogValue
     */
    public function newEntity()
    {
        return new EventLog();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\EventLogRepository
     */
    public function getRepo()
    {
        return $this->getModel()->getEventLogRepository();
    }
}
