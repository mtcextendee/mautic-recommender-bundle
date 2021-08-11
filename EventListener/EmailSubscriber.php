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

use Mautic\CoreBundle\Helper\BuilderTokenHelperFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Entity\Stat;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Exception\FailedToSendToContactException;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailSubscriber implements EventSubscriberInterface
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
     * @var BuilderTokenHelperFactory
     */
    private $builderTokenHelperFactory;

    /**
     * EmailSubscriber constructor.
     */
    public function __construct(
        RecommenderHelper $recommenderHelper,
        RecommenderTokenReplacer $recommenderTokenReplacer,
        IntegrationHelper $integrationHelper,
        EmailModel $emailModel,
        BuilderTokenHelperFactory $builderTokenHelperFactory
    ) {
        $this->recommenderHelper         = $recommenderHelper;
        $this->recommenderTokenReplacer  = $recommenderTokenReplacer;
        $this->integrationHelper         = $integrationHelper;
        $this->emailModel                = $emailModel;
        $this->builderTokenHelperFactory = $builderTokenHelperFactory;
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
     */
    public function onPageBuild(EmailBuilderEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || false === $integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }

        if ($event->tokensRequested(RecommenderHelper::$recommenderRegex)) {
            $tokenHelper = $this->builderTokenHelperFactory->getBuilderTokenHelper('recommender', 'recommender:recommender');
            $event->addTokensFromHelper($tokenHelper, RecommenderHelper::$recommenderRegex, 'name', 'id', true);
        }
    }

    public function onEmailDisplay(EmailSendEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || false === $integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }

        $this->onEmailGenerate($event);
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || false === $integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }
        if ($event->getEmail() && $event->getEmail()->getId() && !empty($event->getLead()['id'])) {
            $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($event->getLead()['id']);
            $this->recommenderTokenReplacer->getRecommenderToken()->setContent($event->getContent());
            $replacedTokens = $this->recommenderTokenReplacer->getReplacedTokensFromContent($event->getContent().'Email');
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
            $event->addTokens($replacedTokens);
        }
    }
}
