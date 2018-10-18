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

class Property extends ItemBase
{
    /** @var \MauticPlugin\MauticRecommenderBundle\Entity\ItemPropertyRepository */
    protected $repo;

    /**
     * @var ItemPropertyModel
     */
    protected $model;

    /**
     * Property constructor.
     *
     * @param array             $options
     * @param ItemPropertyModel $itemPropertyModel
     */
    public function __construct(array $options, ItemPropertyModel $itemPropertyModel)
    {
        $this->model   = $itemPropertyModel;
        $this->repo    = $itemPropertyModel->getRepository();
        $this->options = $options;
    }


}

