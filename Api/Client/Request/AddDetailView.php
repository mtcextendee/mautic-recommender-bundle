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

use MauticPlugin\MauticRecommender\Exception\ItemIdNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Entity\EventLog;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;

class AddDetailView extends AbstractRequest
{

    /**
     * Find exist entity
     *
     * @return null|object
     */
    public function findExist()
    {
        $addEvent = $this->getClient()->send('AddEvent', $this->getOptions());
        /** @var AddEventLog $addEventLog */
        $addEventLog = $this->getClient()->send('AddEventLog', $this->getOptions());
        return false;
    }

    /**
     * Just return new entity
     *
     * @return Item
     */
    public function newEntity()
   {
       return new EventLog();
   }

    public function getRepo()
   {
       return $this->getModel()->getEventLogRepository();
   }
}

