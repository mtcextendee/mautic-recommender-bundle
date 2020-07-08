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
    public function run()
    {
        $addEvent = $this->getClient()->send('AddEvent', ['name'=> $this->getClient()->getEndpoint()]);
        $event    = $addEvent->addIfNotExist();
        $addEvent->save();

        $addEventLog = $this->getClient()->send('AddEventLog', $this->getOptionsResolver()->getOptionsWithEntities(['itemId', 'contactId'], ['event'=> $event]));
        $eventLog    = $addEventLog->add();
        $addEventLog->save();

        $addEventLogPropertyValues = $this->getClient()->send('AddEventLogPropertyValues', $this->getOptionsResolver()->getOptionsWithEntities([], ['eventLog'=> $eventLog]));
        $addEventLogPropertyValues->add();
        $addEventLogPropertyValues->save();

        return false;
    }
}
