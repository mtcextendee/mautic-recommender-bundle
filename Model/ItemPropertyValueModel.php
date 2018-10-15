<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Model;

use Mautic\CoreBundle\Model\AbstractCommonModel;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemProperty;

class ItemPropertyValueModel extends AbstractCommonModel
{
    use ItemModelTrait;

    /**
     * Get this model's repository.
     *
     * @return \MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValue
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:ItemPropertyValue');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyRepository
     */
    public function getItemPropertyRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:ItemProperty');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository
     */
    public function getItemRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Item');
    }



    /**
     * @return Item
     */
    public function newEntity()
    {
        return new ItemProperty();
    }

}
