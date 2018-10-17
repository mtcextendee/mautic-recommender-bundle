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

use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommender\Exception\ItemIdNotFoundException;
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyModel;
use MauticPlugin\MauticRecommenderBundle\Model\ItemPropertyValueModel;

class PropertyValue extends ItemBase
{
    /** @var \MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyValueRepository */
    protected $repo;

    /**
     * @var ItemPropertyModel
     */
    protected $model;

    /** @var  array */
    protected $options;

    /** @var  array */
    protected $properties;


    /**
     * Property constructor.
     *
     * @param array             $options
     * @param ItemPropertyModel $itemPropertyValueModel
     */
    public function __construct(array $options, ItemPropertyValueModel $itemPropertyValueModel)
    {
        $this->model   = $itemPropertyValueModel;
        $this->repo    = $itemPropertyValueModel->getRepository();
        $this->options = $options;
        $this->properties = $this->model->getItemPropertyRepository()->getEntities(
            [
                'hydration_mode' => 'HYDRATE_ARRAY',
            ]
        );

    }
}

