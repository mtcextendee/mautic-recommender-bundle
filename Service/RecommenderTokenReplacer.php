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

    private $replacedTokens = [];

    /**
     * RecommenderTokenReplacer constructor.
     */
    public function __construct(
        RecommenderToken $recommenderToken,
        RecommenderTokenFinder $recommenderTokenFinder,
        RecommenderGenerator $recommenderGenerator
    ) {
        $this->recommenderToken       = $recommenderToken;
        $this->recommenderTokenFinder = $recommenderTokenFinder;
        $this->recommenderGenerator   = $recommenderGenerator;
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
     * @param string $content
     *
     * @return string
     */
    public function replaceTokensFromContent($content)
    {
        $replacedTokens = $this->getReplacedTokensFromContent($content);
        foreach ($replacedTokens as $token=>$tokenContent) {
            $content = str_replace($token, $tokenContent, $content);
        }

        return $content;
    }

    /**
     * @return mixed|string
     */
    public function getReplacedContent($view = 'Page')
    {
        $content        = $this->getRecommenderToken()->getContent();
        $replacedTokens = $this->getReplacedTokensFromContent($content, $view);
        foreach ($replacedTokens as $token => $replace) {
            $content = str_replace($token, $replace, $content);
        }

        return $content;
    }

    public function getReplacedTokensFromContent($content, $view = 'Page')
    {
        $tokens = $this->recommenderTokenFinder->findTokens($content);
        if (!empty($tokens)) {
            /** @var RecommenderToken $token * */
            foreach ($tokens as $key => $token) {
                $tokenContent = $this->recommenderGenerator->getContentByToken($token, $view);
                if (!empty($tokenContent)) {
                    $this->replacedTokens[$key] = $tokenContent;
                } else {
                    $this->replacedTokens[$key] = '';
                }
            }
        }

        return $this->replacedTokens;
    }

    public function replaceTagsFromContent($content, RecommenderToken $recommenderToken)
    {
        $this->recommenderGenerator->getResultByToken($recommenderToken);

        return $this->recommenderGenerator->replaceTagsFromContent($content);
    }

    /**
     * @return bool
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
