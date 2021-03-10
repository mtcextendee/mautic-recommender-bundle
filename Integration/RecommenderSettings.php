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
use MauticPlugin\MauticRecommenderBundle\Enum\PropertyTypeEnum;
use MauticPlugin\MauticRecommenderBundle\Integration\DTO\RecombeeSettings;
use MauticPlugin\MauticRecommenderBundle\Logger\DebugLogger;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderPropertyModel;

class RecommenderSettings
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var RecommenderPropertyModel
     */
    private $propertyModel;

    /**
     * EcrSettings constructor.
     */
    public function __construct(IntegrationHelper $integrationHelper, CoreParametersHelper $coreParametersHelper, RecommenderEventModel $eventModel, RecommenderPropertyModel $propertyModel)
    {
        $this->integrationHelper    = $integrationHelper;
        $this->settings             = $this->getIntegrationSettings('Recommender');
        $this->propertyModel        = $propertyModel;
    }

    public function isEnabled()
    {
        return !empty($this->settings);
    }

    /**
     * @param $integrationName
     *
     * @return array
     */
    private function getIntegrationSettings($integrationName)
    {
        $integration = $this->integrationHelper->getIntegrationObject($integrationName);
        if ($integration instanceof AbstractIntegration && $integration->getIntegrationSettings()->getIsPublished()) {
            return array_merge(
                $integration->getDecryptedApiKeys(),
                $integration->mergeConfigToFeatureSettings()
            );
        }

        return [];
    }

    /**
     * @return int|void
     */
    public function getPropertyCategoryId()
    {
        if ($property = $this->propertyModel->getRepository()->findOneBy(['name' => 'category'])) {
            return $property->getId();
        }
    }

    /**
     * @return int|void
     */
    public function getPropertyPriceId()
    {
        if ($property = $this->propertyModel->getRepository()->findOneBy(['name' => PropertyTypeEnum::PRICE])) {
            return $property->getId();
        }
    }

    public function initiateDebugLogger(DebugLogger $logger): void
    {
        // Yes it's a hack to prevent from having to pass the logger as a dependency into dozens of classes
        // So not doing anything with the logger, just need Symfony to initiate the service
    }
}
