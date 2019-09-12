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
     * @var CorePermissions
     */
    private $security;

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
     * Processor constructor.
     *
     * @param CoreParametersHelper  $coreParametersHelper
     * @param CorePermissions       $security
     * @param ApiCommands           $apiCommands
     * @param RecommenderEventModel $eventModel
     * @param TranslatorInterface   $translator
     * @param LeadModel             $leadModel
     */
    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        CorePermissions $security,
        ApiCommands $apiCommands,
        RecommenderEventModel $eventModel,
        TranslatorInterface $translator,
        LeadModel $leadModel
    ) {

        $this->coreParametersHelper = $coreParametersHelper;
        $this->security             = $security;
        $this->apiCommands          = $apiCommands;
        $this->eventModel           = $eventModel;
        $this->translator           = $translator;
        $this->leadModel            = $leadModel;
    }

    /**
     * @param array|null $eventDetail
     *
     * @return bool
     * @throws \Exception
     */
    public function process($eventDetail)
    {
        if (empty($eventDetail)) {
            throw new \Exception('Event detail of tracking event cannot be empty');
        }

        if (!$this->security->isAnonymous()) {
            throw new \Exception('Can\'t load for loggedin users');
        }

        $eventLabel = $this->coreParametersHelper->getParameter('eventLabel');

        if (!isset($eventDetail['eventName'])) {
            throw new \Exception(
                $this->translator->trans('mautic.plugin.recommender.eventName.not_found', [], 'validators')
            );
        } elseif (!$this->eventModel->getRepository()->findOneBy(['name' => $eventDetail['eventName']])) {
            throw new \Exception(
                $this->translator->trans(
                    'mautic.plugin.recommender.eventName.not_allowed',
                    [
                        '%eventName%' => $eventDetail['eventName'],
                    ],
                    'validators'
                )
            );
        }
        // Just provider from console
        if (defined('IN_MAUTIC_CONSOLE')) {
            if (isset($eventDetail['contactEmail'])) {
                $contact = $this->leadModel->getRepository()->getContactsByEmail($eventDetail['contactEmail']);
                $contact = current($contact);
                if (!$contact instanceof Lead) {
                    throw new \Exception('Contact '.$eventDetail['contactEmail'].' not found');
                }
                unset($eventDetail['contactEmail']);
                $this->leadModel->setSystemCurrentLead($contact);
            } elseif (isset($eventDetail['contactId'])) {
                $this->leadModel->setSystemCurrentLead($this->leadModel->getEntity($eventDetail['contactId']));
            }
        }

        $this->apiCommands->callCommand($eventLabel, $eventDetail);

        return true;
    }

}

