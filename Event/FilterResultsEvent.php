<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use Symfony\Component\Form\FormBuilderInterface;

class FilterResultsEvent extends CommonEvent
{
    /**
     * @var RecommenderToken
     */
    private $recommenderToken;
    private $items = [];

    public function __construct(RecommenderToken $recommenderToken)
    {
        $this->recommenderToken = $recommenderToken;
    }

    /**
     * @return RecommenderToken
     */
    public function getRecommenderToken()
    {
        return $this->recommenderToken;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
