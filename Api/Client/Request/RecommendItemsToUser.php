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
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Entity\EventLog;
use MauticPlugin\MauticRecommenderBundle\Entity\Item;

class RecommendItemsToUser extends AbstractRequest
{
    public function run()
    {
        $results = $this->getModel()->getRepository()->getContactsItemsByPoints($this->getOptions()['userId'], $this->getOptions()['limit']);
        foreach ($results as &$result) {
            $properties           = $this->getModel()->getItemPropertyValueRepository()->getValues($result['id']);
            $properties           = array_combine(array_column($properties, 'name'), array_column($properties, 'value'));
            $translatedProperties = [];
            foreach ($properties as $property=>$value) {
                $translatedProperties[InputHelper::alphanum(InputHelper::transliterate($property))] = $value;
            }
            $result = array_merge($result, $translatedProperties);
        }

        return $results;
    }
}
