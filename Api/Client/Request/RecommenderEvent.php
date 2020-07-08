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

class RecommenderEvent extends AbstractRequest
{
    public function run()
    {
        $event       = $this->getModel()->getEventRepository()->findOneBy(['name' => $this->getOptions()['eventName']]);
        $options     = $this->getOptionsResolver()->getOptionsWithEntities(['itemId', 'contactId', 'userId', 'dateAdded'], ['event'=> $event]);
        $addEventLog = $this->getClient()->send('AddEventLog', $options);
        $eventLog    = $addEventLog->add();
        $addEventLog->save();

        $addEventLogPropertyValues = $this->getClient()->send('AddEventLogPropertyValues', $this->getOptionsResolver()->getOptionsWithEntities([], ['eventLog'=> $eventLog]));
        $addEventLogPropertyValues->add();
        $addEventLogPropertyValues->save();

        return false;
    }
}
