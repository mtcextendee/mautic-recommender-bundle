<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Controller;

use Mautic\CoreBundle\Exception as MauticException;
use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecommenderController extends AbstractStandardFormController
{

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'recommender.recommender';
    }


    /**
     * @return JsonResponse
     */
    public function sendAction()
    {
        if (!$this->get('mautic.security')->isAnonymous()) {
            return new JsonResponse(
                [
                    'success' => 0,
                ]
            );
        }

        /** @var ApiCommands $apiCommands */
        $apiCommands = $this->get('mautic.recommender.service.api.commands');
        $eventLabel  = $this->get('mautic.helper.core_parameters')->getParameter('eventLabel');
        /** @var RecommenderEventModel $eventModel */
        $eventModel = $this->getModel('recommender.event');

        $integrationSettings = $this->get('mautic.helper.integration')->getIntegrationObject(
            'Recommender'
        )->getIntegrationSettings()->getFeatureSettings();
        $options             = $this->request->request->all();
        $recommender         = $this->request->get('eventDetail');
        $eventDetail         = json_decode(base64_decode($recommender), true);
        $error               = false;

        if (!isset($eventDetail['eventName'])) {
            $error = $this->get('translator')->trans('mautic.plugin.recommender.eventName.not_found', [], 'validators');
        } elseif (!$eventModel->getRepository()->findOneBy(['name' => $eventDetail['eventName']])) {
            $error = $this->get('translator')->trans(
                'mautic.plugin.recommender.eventName.not_allowed',
                [
                    '%eventName%' => $eventDetail['eventName'],
                ],
                'validators'
            );
        }

        $response = ['success' => !(bool) $error,];
        if (!$error) {
            $apiCommands->callCommand($eventLabel, $eventDetail);
        } else {
            $response['message'] = $error;
        }

        return new JsonResponse(
            [
                $response,
            ]
        );
    }
}
