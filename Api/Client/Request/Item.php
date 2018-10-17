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
use MauticPlugin\MauticRecommenderBundle\Entity\ItemRepository;
use MauticPlugin\MauticRecommenderBundle\Model\ItemModel;

class Item extends ItemBase
{
    /**
     * @var ItemModel
     */
    protected $model;

    /** @var  ItemRepository */
    protected $repo;

    /** @var  \MauticPlugin\MauticRecommenderBundle\Entity\Item */
    protected $entity;

    /**
     * @var array
     */
    protected $options;

    /**
     * ItemRequest constructor.
     *
     * @param array     $options
     * @param ItemModel $itemModel
     */
    public function __construct(array $options, ItemModel $itemModel)
    {
        $this->model   = $itemModel;
        $this->repo    = $itemModel->getRepository();
        $this->options = $options;
    }
}

