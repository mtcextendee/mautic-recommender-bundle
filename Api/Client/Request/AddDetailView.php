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
        $event = $this->getModel()->getEventRepository()->findOneBy(['name' => __CLASS__]);
        // If event name already not exist
        if (!$event) {
            $event = new Event();
            $event->setName(__CLASS__);
            $this->getModel()->getEventRepository()->saveEntity($event);
        }

        $this->addOption('event', $event);

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

    /**
     * @return \MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository
     */
    public function getRepo()
   {
       return $this->getModel()->getRepository();
   }
}

