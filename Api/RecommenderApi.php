<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Templating\Helper\VersionHelper;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Api\Client\Client;
use Monolog\Logger;

class RecommenderApi
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * TwilioApi constructor.
     *
     * @internal param CoreParametersHelper $coreParametersHelper
     */
    public function __construct(
        TrackableModel $pageTrackableModel,
        IntegrationHelper $integrationHelper,
        Logger $logger,
        VersionHelper $versionHelper,
        Client $client
    ) {
        $this->logger = $logger;
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
