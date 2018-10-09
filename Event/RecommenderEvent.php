<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticMTCPilotBundle\Entity\MTCPilot;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;

class RecommenderEvent extends CommonEvent
{
    /**
     * RecommenderEvent constructor.
     *
     * @param Recommender $entity
     * @param bool           $isNew
     */
    public function __construct(Recommender $entity, $isNew = false)
    {
        $this->entity = $entity;
        $this->isNew  = $isNew;
    }

    /**
     * @return Recommender
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Recommender $entity
     */
    public function setEntity(Recommender $entity)
    {
        $this->entity = $entity;
    }
}
