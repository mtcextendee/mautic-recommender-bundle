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

use Joomla\Http\Http;
use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\PageBundle\Model\TrackableModel;

abstract class AbstractRecommenderApi
{
    /**
     * @var MauticFactory
     */
    protected $pageTrackableModel;

    /**
     * AbstractSmsApi constructor.
     *
     * @param TrackableModel $pageTrackableModel
     */
    public function __construct(TrackableModel $pageTrackableModel)
    {
        $this->pageTrackableModel = $pageTrackableModel;
    }


    /**
     * Convert a non-tracked url to a tracked url.
     *
     * @param string $url
     * @param array  $clickthrough
     *
     * @return string
     */
    public function convertToTrackedUrl($url, array $clickthrough = [])
    {
        /* @var \Mautic\PageBundle\Entity\Redirect $redirect */
        $trackable = $this->pageTrackableModel->getTrackableByUrl($url, 'recommender', $clickthrough['recommender']);

        return $this->pageTrackableModel->generateTrackableUrl($trackable, $clickthrough, true);
    }
}
