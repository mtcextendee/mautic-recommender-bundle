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
use MauticPlugin\MauticRecommenderBundle\Entity\PropertyRepository;

class RecommenderPropertyModel extends AbstractCommonModel
{
    /**
     * Retrieve the permissions base.
     *
     * @return string
     */
    public function getPermissionBase()
    {
        return 'recommender:recommender';
    }

    /**
     * {@inheritdoc}
     *
     * @return PropertyRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticRecommenderBundle:Property');
    }
}
