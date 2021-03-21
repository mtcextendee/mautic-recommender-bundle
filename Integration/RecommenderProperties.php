<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Integration;

use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticRecommenderBundle\Enum\EventTypeEnum;
use MauticPlugin\MauticRecommenderBundle\Enum\PropertyTypeEnum;
use MauticPlugin\MauticRecommenderBundle\Integration\DTO\RecombeeSettings;
use MauticPlugin\MauticRecommenderBundle\Logger\DebugLogger;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderPropertyModel;

class RecommenderProperties
{
    /**
     * @var RecommenderPropertyModel
     */
    private $propertyModel;

    /**
     * @var RecommenderEventModel
     */
    private $eventModel;

    /**
     * @var int|null
     */
    private $addToCartEventId;

    /**
     * @var int|null
     */
    private $removeFromCartEventId;

    /**
     * @var int|null
     */
    private $purchaseEventId;

    /**
     * @var int|null
     */
    private $categoryPropertyId;

    public function __construct(RecommenderPropertyModel $propertyModel, RecommenderEventModel $eventModel)
    {
        $this->propertyModel = $propertyModel;
        $this->eventModel = $eventModel;
    }

    public function getAddToCartEventId(): ?int
    {
        if ($this->addToCartEventId === null) {
            $this->addToCartEventId = $this->getEventIdByAlias(EventTypeEnum::CART_ADDITIONS);
        }

        return $this->addToCartEventId;
    }

    private function getEventIdByAlias(string $type)
    {
        if ($recommenderEvent = $this->eventModel->getRepository()->findOneBy(
            ['type' => $type]
        )) {
            return  $recommenderEvent->getId();
        }
    }

    public function getRemoveFromCartEventId(): ?int
    {
        if ($this->removeFromCartEventId === null) {
                $this->removeFromCartEventId = $this->getEventIdByAlias(EventTypeEnum::CART_REMOVE);
        }

        return $this->removeFromCartEventId;
    }

    public function getPurchaseEventId(): ?int
    {
        if ($this->purchaseEventId === null) {
            $this->purchaseEventId = $this->getEventIdByAlias(EventTypeEnum::PURCHASE);
        }

        return $this->purchaseEventId;
    }

    public function getCategoryPropertyId()
    {
        if ($this->categoryPropertyId === null) {
            $this->categoryPropertyId = $this->getPropertyIdByAlias(PropertyTypeEnum::CATEGORY);
        }

        return $this->categoryPropertyId;
    }

    private function getPropertyIdByAlias(string $type)
    {
        if ($property = $this->propertyModel->getRepository()->findOneBy(
            ['name' => $type]
        )) {
            return  $property->getId();
        }
    }
}
