<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Api\Service;

use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;

class AbandonedCartSync extends ApiCommands
{


    public function getCart()
    {

    }



    public function sync($apiRequest, array $options)
    {
        if ($apiRequest == 'AddPurchase') {
        }
// vlozim do kosika - cakam 18 hod
        // odosielam email
        // odstranujem z kosika - kontrolujem stav kosika v case x-y
        //  ak je 0 tak, odstranujem zo segmentu
        // ak je objednavka, tak 

        // If purchase
        // Remove All
        //  $this->callCommand('ListUserCartAdditions', ['user'])
        ///   $client->send(new DeleteCartAddition($user_id, $item_id, [ //optional parameters:
        //            'timestamp' => <number>
//]));

    }

}

