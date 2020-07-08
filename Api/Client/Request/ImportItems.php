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

use Mautic\CoreBundle\Helper\DateTimeHelper;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;

class ImportItems extends AbstractRequest
{
    public function run()
    {
        $timeout = $this->getSetting('timeout');
        /** @var AddItem $addItem */
        $addItem = $this->getClient()->send('AddItem', ['item_id'=> $this->getOptions()['itemId']]);
        /** @var Item $item */
        $item = $addItem->addIfNotExist();
        if (!empty($timeout) && $item->getId()) {
            if ((new DateTimeHelper($timeout))->getDateTime() < $item->getDateModified()) {
                return 0;
            }
        }
        $item->setDateModified((new DateTimeHelper())->getDateTime());
        $item->setActive(true);
        $addItem->addEntity($item);
        $addItem->save();

        $addEventLogPropertyValues = $this->getClient()->send('AddItemValues', $this->getOptionsResolver()->getOptionsWithEntities([], ['item'=> $item]));
        $addEventLogPropertyValues->add();
        $addEventLogPropertyValues->save();
        $addEventLogPropertyValues->delete();

        return 1;
    }
}
