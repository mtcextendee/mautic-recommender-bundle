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

class RecommenderTagsReplacer
{
    /**
     * @var RecommenderToken
     */
    private $recommenderToken;

    private $options;

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * RecommenderTagsReplacer constructor.
     *
     * @param array $options
     */
    public function __construct(RecommenderTokenReplacer $recommenderTokenReplacer, RecommenderToken $recommenderToken, $options = [])
    {
        $this->recommenderToken         = $recommenderToken;
        $this->options                  = $options;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function replaceTags($content)
    {
        return $this->recommenderTokenReplacer->replaceTagsFromContent($content, $this->recommenderToken, $this->options);
    }
}
