<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Events;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use Symfony\Component\Translation\TranslatorInterface;

class Processor
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var ApiCommands
     */
    private $apiCommands;

    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    /**
     * Processor constructor.
     */
    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        CorePermissions $security,
        ApiCommands $apiCommands,
        RecommenderEventModel $eventModel,
        TranslatorInterface $translator,
        LeadModel $leadModel,
        ContactTracker $contactTracker
    ) {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->apiCommands          = $apiCommands;
        $this->eventModel           = $eventModel;
        $this->translator           = $translator;
        $this->leadModel            = $leadModel;
        $this->contactTracker       = $contactTracker;
    }

    /**
     * @param array|null $eventDetail
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function process($eventDetail)
    {
        if (empty($eventDetail)) {
            throw new \Exception('Event detail of tracking event cannot be empty');
        }

        $eventLabel = $this->coreParametersHelper->getParameter('eventLabel');

        if (!isset($eventDetail['eventName'])) {
            throw new \Exception($this->translator->trans('mautic.plugin.recommender.eventName.not_found', [], 'validators'));
        } elseif (!$this->eventModel->getRepository()->findOneBy(['name' => $eventDetail['eventName']])) {
            throw new \Exception($this->translator->trans('mautic.plugin.recommender.eventName.not_allowed', ['%eventName%' => $eventDetail['eventName']], 'validators'));
        }
        // Just provider from console
        $inConsole = defined('IN_MAUTIC_CONSOLE') || defined('IN_MAUTIC_API');
        if (isset($eventDetail['contactEmail'])) {
            $contact = $this->leadModel->checkForDuplicateContact(['email'=>$eventDetail['contactEmail']]);
            if (!$contact instanceof Lead) {
                throw new \Exception('Contact '.$eventDetail['contactEmail'].' not found');
            }
            unset($eventDetail['contactEmail']);
            $this->contactTracker->setSystemContact($contact);
        } elseif (isset($eventDetail['contactId'])) {
            if ($contact = $this->leadModel->getEntity($eventDetail['contactId'])) {
                $this->contactTracker->setSystemContact($contact);
            } else {
                throw new \Exception('Tracked contact doesn\'t exist');
            }
        } elseif (!$inConsole) {
            if ($contact = $this->contactTracker->getContact()) {
                $eventDetail['contactId'] = $contact->getId();
            } else {
                throw new \Exception('Tracked contact doesn\'t exist');
            }
        } elseif ($inConsole) {
            throw new \Exception('One of parameters contactId/contactEmail is required');
        }

        $this->apiCommands->callCommand($eventLabel, $eventDetail);

        return true;
    }
}
