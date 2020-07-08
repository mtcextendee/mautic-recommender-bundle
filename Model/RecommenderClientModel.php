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
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Tracker\ContactTracker;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;

class RecommenderClientModel extends AbstractCommonModel
{
    /**
     * @var ContactTracker
     */
    private $contactTracker;

    /**
     * RecommenderClientModel constructor.
     *
     * @param ContactTracker $contactTracker
     */
    public function __construct(ContactTracker $contactTracker)
    {
        $this->contactTracker = $contactTracker;
    }

    /**
     * Get this model's repository.
     *
     * @return \MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Item');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\PropertyRepository
     */
    public function getPropertyRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Property');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValueRepository
     */
    public function getItemPropertyValueRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:ItemPropertyValue');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\EventRepository
     */
    public function getEventRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Event');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\EventLogRepository
     */
    public function getEventLogRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:EventLog');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticRecommenderBundle\Entity\EventLogValueRepository
     */
    public function getEventLogValueRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:EventLogValue');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\Mautic\LeadBundle\Entity\LeadRepository
     */
    public function getContactRepository()
    {
        return $this->em->getRepository('MauticLeadBundle:Lead');
    }

    /**
     * @return Lead|null
     */
    public function getCurrentContact()
    {
        return $this->contactTracker->getContact();
    }
}
