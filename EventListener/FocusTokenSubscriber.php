<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CampaignBundle\Model\EventModel;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Event as Events;
use Mautic\LeadBundle\LeadEvent;
use MauticPlugin\MauticFocusBundle\Entity\Focus;
use MauticPlugin\MauticFocusBundle\FocusEvents;
use MauticPlugin\MauticFocusBundle\Model\FocusModel;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class FocusTokenSubscriber.
 */
class FocusTokenSubscriber extends CommonSubscriber
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var FocusModel
     */
    private $focusModel;

    /**
     * FocusSubscriber constructor.
     *
     * @param Session    $session
     * @param EventModel $eventModel
     * @param FocusModel $focusModel
     */
    public function __construct(Session $session, EventModel $eventModel, FocusModel $focusModel)
    {

        $this->session = $session;
        $this->eventModel = $eventModel;
        $this->focusModel = $focusModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FocusEvents::TOKEN_REPLACEMENT => ['onTokenReplacement', 200],
        ];
    }

    /**
     * @param TokenReplacementEvent $event
     */
    public function onTokenReplacement(TokenReplacementEvent $event)
    {
        if (!$this->request->get('recommender') || empty($event->getClickthrough()['focus_id'])) {
            return;
        }
        $focus = $this->focusModel->getEntity($event->getClickthrough()['focus_id']);
        if (!$focus) {
            return;
        }
        /** @var Lead $lead */
        $content = $event->getContent();
        if ($content) {
            $tokensFromSession = $this->session->get($this->request->get('recommender'));
            if (!$tokensFromSession) {
                for ($i = 0; $i < 2; $i++) {
                    sleep(1);
                    $tokensFromSession = $this->session->get($this->request->get('recommender'));
                    if ($tokensFromSession) {
                        break;
                    }
                }
            }
            $tokens = unserialize($tokensFromSession);
            $content = str_replace(array_keys($tokens), array_values($tokens), $content);
            $event->setContent($content);
        }
    }
}
