<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class PropertyRepository extends CommonRepository
{
    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'rip';
    }

    /**
     * @return array
     */
    public function findAllArray()
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->from('MauticRecommenderBundle:Property', 'p')
                ->select('p')
                ->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getPropertyNamesAsChoices()
    {
        $properties = $this->findAllArray();

        return array_combine(array_column($properties, 'id'), array_column($properties, 'name'));
    }
}
