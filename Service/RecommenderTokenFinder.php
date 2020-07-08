<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Service;

use Mautic\PageBundle\Event\PageDisplayEvent;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class RecommenderTokenFinder
{
    private $recommenderTokens = [];

    private $recommenderRegex = '{recommender=(.*?)}';

    /**
     * @var RecommenderToken
     */
    private $recommenderToken;

    public function __construct(RecommenderToken $recommenderToken)
    {
        $this->recommenderToken = $recommenderToken;
    }

    public function findTokens($content)
    {
        $regex   = '/'.$this->recommenderRegex.'/i';
        preg_match_all($regex, $content, $matches);
        if (empty($matches[1])) {
            return;
        }
        foreach ($matches[1] as $key => $match) {
            $this->recommenderToken->setId($match);
            $this->recommenderTokens[$matches[0][$key]] = $this->recommenderToken;
        }

        return $this->recommenderTokens;
    }

    /**
     * @return array
     */
    public function getRecommenderTokens()
    {
        return $this->recommenderTokens;
    }

    /**
     * @param array $recommenderTokens
     */
    public function setRecommenderTokens(array $recommenderTokens)
    {
        $this->recommenderTokens = $recommenderTokens;
    }
}
