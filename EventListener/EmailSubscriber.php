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
use Mautic\EmailBundle\Entity\Stat;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Exception\EmailCouldNotBeSentException;
use Mautic\EmailBundle\Exception\FailedToSendToContactException;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
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
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * EmailSubscriber constructor.
     *
     * @param RecommenderHelper        $recommenderHelper
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     * @param IntegrationHelper        $integrationHelper
     * @param EmailModel               $emailModel
     */
    public function __construct(
        RecommenderHelper $recommenderHelper,
        RecommenderTokenReplacer $recommenderTokenReplacer,
        IntegrationHelper $integrationHelper,
        EmailModel $emailModel
    ) {
        $this->recommenderHelper        = $recommenderHelper;
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->integrationHelper        = $integrationHelper;
        $this->emailModel = $emailModel;
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
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

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
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

        $this->onEmailGenerate($event);
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(EmailSendEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

        if ($event->getEmail() && $event->getEmail()->getId() && !empty($event->getLead()['id'])) {
            $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($event->getLead()['id']);
            $this->recommenderTokenReplacer->getRecommenderToken()->setContent($event->getContent());
            $replacedTokens = $this->recommenderTokenReplacer->getReplacedTokensFromContent('Email');


            if (count(array_filter($replacedTokens)) != count($replacedTokens)) {
                /** @var Stat $stat */
                if ($stat = $this->emailModel->getStatRepository()->findOneBy(
                    ['trackingHash' => $event->getIdHash()]
                )) {
                    $stat->setIsFailed(true);
                    $this->emailModel->getStatRepository()->saveEntity($stat);
                }

                throw new FailedToSendToContactException();
            }

            if ($content = $this->recommenderTokenReplacer->getReplacedContent('Email')) {
                $event->setContent($content);
            }
            if ($subject = $this->recommenderTokenReplacer->getRecommenderGenerator()->replaceTagsFromContent($event->getSubject())) {
                $event->setSubject($subject);
            }
        }
    }
}
