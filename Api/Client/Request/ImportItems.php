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

class ImportItems extends AbstractRequest
{
    public function run()
    {
        $addItem = $this->getClient()->send('AddItem', ['item_id'=> $this->getOptions()['itemId']]);
        $item = $addItem->addIfNotExist();
        $addItem->save();

        $addEventLogPropertyValues = $this->getClient()->send('AddItemValues', $this->getOptionsResolver()->getOptionsWithEntities([], ['item'=> $item]));
        $addEventLogPropertyValues->add();
        $addEventLogPropertyValues->save();
        $addEventLogPropertyValues->delete();

        return false;
    }

}

