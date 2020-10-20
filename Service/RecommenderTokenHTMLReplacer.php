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

class RecommenderTokenHTMLReplacer
{
    /**
     * @var RecommenderGenerator
     */
    private $recommenderGenerator;
    private $recommenderToken;

    /**
     * RecommenderTokenHTMLReplacer constructor.
     */
    public function __construct(RecommenderGenerator $recommenderGenerator, RecommenderToken $recommenderToken)
    {
        $this->recommenderGenerator = $recommenderGenerator;
        $this->recommenderToken     = $recommenderToken;
    }

    public function findTokens($content)
    {
        // replace slots
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        $xpath = new \DOMXPath($dom);

        $divContent = $xpath->query('//*[@class="mautic-recommender"]');
        for ($i = 0; $i < $divContent->length; ++$i) {
            $recommenderBlock = $divContent->item($i);
            //$this->recommenderToken->setToken($this->parseData($recommenderBlock));
            $newContent = $this->recommenderGenerator->getContentByToken($this->recommenderToken, $content);
            $newnode    = $dom->createDocumentFragment();
            $newnode->appendXML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            // in case we want to just change the slot contents:
            // $slot->appendChild($newnode);
            $recommenderBlock->parentNode->replaceChild($newnode, $newContent);
        }
        $dom->saveHTML();
    }
}
