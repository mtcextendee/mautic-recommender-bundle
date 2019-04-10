<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\BuilderTokenHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber extends CommonSubscriber
{
    /**
     * @var RecommenderHelper
     */
    protected $recommenderHelper;

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;


    /**
     * EmailSubscriber constructor.
     *
     * @param RecommenderHelper        $recommenderHelper
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     */
    public function __construct(RecommenderHelper $recommenderHelper, RecommenderTokenReplacer $recommenderTokenReplacer)
    {
        $this->recommenderHelper        = $recommenderHelper;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_BUILD   => ['onPageBuild', 0],
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailDisplay', 0],
        ];
    }

    /**
     * Add email to available page tokens.
     *
     * @param EmailBuilderEvent $event
     */
    public function onPageBuild(EmailBuilderEvent $event)
    {
        if ($event->tokensRequested(RecommenderHelper::$recommenderRegex)) {
            $tokenHelper = new BuilderTokenHelper($this->factory, 'recommender');
            $event->addTokensFromHelper($tokenHelper, RecommenderHelper::$recommenderRegex, 'name', 'id', true);
        }
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailDisplay(EmailSendEvent $event)
    {
        $this->onEmailGenerate($event);
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(EmailSendEvent $event)
    {
        if ($event->getEmail() && $event->getEmail()->getId() && !empty($event->getLead()['id'])) {
            $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($event->getLead()['id']);
            $this->recommenderTokenReplacer->getRecommenderToken()->setContent($event->getContent());
            if ($content = $this->recommenderTokenReplacer->getReplacedContent()){
                $event->setContent($content);
            }
            if ($subject = $this->recommenderTokenReplacer->getRecommenderGenerator()->replaceTagsFromContent($event->getSubject())){
                $event->setSubject($subject);
            }
        }
    }
}
