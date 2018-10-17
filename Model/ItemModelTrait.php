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

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * trait ItemModelTrait.
 */
trait ItemModelTrait
{
    /**
     * @param null $entity
     * @param array     $options
     *
     * @return Item
     */
    public function setValues($entity = null, array $options)
    {
        if ($entity === null) {
            $entity = $this->newEntity();
        }

        $accessor = new PropertyAccessor();
        foreach ($options as $key=>$value){
            try {
                $accessor->setValue($entity, $key, $value);
            } catch (\Exception $exception) {

            }
        }

        return $entity;
    }
}
