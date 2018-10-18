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
use MauticPlugin\MauticRecommenderBundle\Entity\Item;

class AddItem extends AbstractRequest
{

    /**
     * Find exist entity
     *
     * @return null|object
     */
    public function findExist()
   {
       return $this->getRepo()->findOneBy(['itemId' => $this->getOption()['itemId']]);
   }

    /**
     * Just return new entity
     *
     * @return Item
     */
    public function newEntity()
   {
       return new Item();
   }

    /**
     * @return \MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository
     */
    public function getRepo()
   {
       return $this->getModel()->getRepository();
   }
}

