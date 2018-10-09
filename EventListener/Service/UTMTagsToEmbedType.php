<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener\Service;

use Mautic\CampaignBundle\Entity\Campaign;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Model\CampaignModel;
use Mautic\CoreBundle\Helper\InputHelper;

class UTMTagsToEmbedType
{
    private $source = 'MauticRecommender';

    private $campaign = [];

    private $content = [];

    /**
     * UTMTagsToEmbedType constructor.
     *
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        //$this->campaign  =  $campaign->getId().'-'.$this->cleanString($campaign->getName());
        $this->campaign = $campaign->getId().'-*';
    }

    public function setUtm($event)
    {
        //$this->content[$event['channel']][$event['channel_id']] = $this->cleanString($event['name']);
        $this->content[$event['channel']][] = $event['channel'].'-*';
    }

    public function getUtmForFilter()
    {
        $filters = 'ga:source=='.$this->source.';';
        $filters .= 'ga:campaign~^'.$this->campaign.';';
        $medium  = [];
        $content = [];
        foreach ($this->content as $channel => $channelProperties) {
            $medium[] = 'ga:medium=='.$channel;
            foreach ($channelProperties as $cAlias) {
                $content[] = 'ga:content~^'.$cAlias;
            }
        }

        $filters .= implode(',', $medium).';';
        $filters .= implode(',', $content).';';

        return $filters;
    }


    /**
     * @param        $string
     * @param string $spaceCharacter
     *
     * @return mixed|string
     */
    private function cleanString($string, $spaceCharacter = '-')
    {
        // Transliterate to latin characters
        $string = InputHelper::transliterate(trim($string));

        // Some labels are quite long if a question so cut this short
        $string = strtolower(InputHelper::alphanum($string, false, $spaceCharacter));

        return $string;
    }
}