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

use Mautic\PageBundle\Model\TrackableModel;
use Recommender\RecommApi\Exceptions as Ex;
use Recommender\RecommApi\Requests as Reqs;

class RecommenderTokenReplacer
{
    /**
     * @var RecommenderToken
     */
    private $recommenderToken;

    /**
     * @var RecommenderTokenFinder
     */
    private $recommenderTokenFinder;

    /**
     * @var RecommenderGenerator
     */
    private $recommenderGenerator;

    private $replacedTokens;

    /**
     * @var TrackableModel
     */
    private $trackableModel;

    /**
     * RecommenderTokenReplacer constructor.
     *
     * @param RecommenderToken       $recommenderToken
     * @param RecommenderTokenFinder $recommenderTokenFinder
     * @param RecommenderGenerator   $recommenderGenerator
     * @param TrackableModel      $trackableModel
     */
    public function __construct(
        RecommenderToken $recommenderToken,
        RecommenderTokenFinder $recommenderTokenFinder,
        RecommenderGenerator $recommenderGenerator,
        TrackableModel $trackableModel
    ) {
        $this->recommenderToken       = $recommenderToken;
        $this->recommenderTokenFinder = $recommenderTokenFinder;
        $this->recommenderGenerator   = $recommenderGenerator;
        $this->trackableModel = $trackableModel;
    }

    /**
     * @return RecommenderToken
     */
    public function getRecommenderToken()
    {
        return $this->recommenderToken;
    }

    /**
     * @return RecommenderGenerator
     */
    public function getRecommenderGenerator()
    {
        return $this->recommenderGenerator;
    }

    /**
     * @param       $content
     * @param array $options
     *
     * @return mixed
     * @internal param $event
     */
    public function replaceTokensFromContent($content, $options = [])
    {
        $tokens = $this->recommenderTokenFinder->findTokens($content);
        if (!empty($tokens)) {
            /**
             * @var  $key
             * @var  RecommenderToken $token
             */
            foreach ($tokens as $key => $token) {
                $token->setAddOptions($options);
                $tokenContent = $this->recommenderGenerator->getContentByToken($token);
                if (!empty($tokenContent)) {
                    $content      = str_replace($key, $tokenContent, $content);
                    $this->replacedTokens[$key] = $tokenContent;
                }else{
                    // no content, no token
                    $content      = str_replace($key, '', $content);
                }
            }
        }

        return $content;
    }

    /**
     * @param               $content
     * @param RecommenderToken $recommenderToken
     * @param array $options
     */
    public function replaceTagsFromContent($content, RecommenderToken $recommenderToken, $options = [])
    {
        $recommenderToken->setAddOptions($options);
        $this->recommenderGenerator->getResultByToken($recommenderToken, $options);
        $content = $this->recommenderGenerator->replaceTagsFromContent($content);
        $this->replacedTokens[] = $content;

        return $content;
    }


    /**
     * @return boolean
     */
    public function hasItems()
    {
        if (!empty($this->replacedTokens)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getReplacedTokens()
    {
        return $this->replacedTokens;
    }

}

