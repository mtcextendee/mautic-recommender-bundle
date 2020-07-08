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
use MauticPlugin\MauticRecommenderBundle\Integration\DTO\RecombeeSettings;
use MauticPlugin\MauticRecommenderBundle\Logger\DebugLogger;

class RecommenderSettings
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var RecombeeSettings
     */
    private $recombeeSettings;

    /**
     * @var array
     */
    private $settings;

    /**
     * EcrSettings constructor.
     *
     * @param IntegrationHelper    $integrationHelper
     * @param CoreParametersHelper $coreParametersHelper
     */
    public function __construct(IntegrationHelper $integrationHelper, CoreParametersHelper $coreParametersHelper)
    {
        $this->integrationHelper    = $integrationHelper;
        $this->coreParametersHelper = $coreParametersHelper;
        $this->settings             = $this->getIntegrationSettings('Recommender');
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
     * @param DebugLogger $logger
     */
    public function initiateDebugLogger(DebugLogger $logger): void
    {
        // Yes it's a hack to prevent from having to pass the logger as a dependency into dozens of classes
        // So not doing anything with the logger, just need Symfony to initiate the service
    }
}
