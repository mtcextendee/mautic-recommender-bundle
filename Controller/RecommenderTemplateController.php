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

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use Mautic\CoreBundle\Exception as MauticException;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecommenderTemplateController extends AbstractStandardFormController
{
    /**
     * {@inheritdoc}
     */
    protected function getJsLoadMethodPrefix()
    {
        return 'recommenderTemplate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'recommender.template';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteBase()
    {
        return 'recommender_template';
    }

    /***
     * @param null $objectId
     *
     * @return string
     */
    protected function getSessionBase($objectId = null)
    {
        return 'recommender.template'.(($objectId) ? '.'.$objectId : '');
    }

    /**
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticRecommenderBundle:RecommenderTemplate';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        return $this->batchDeleteStandard();
    }

    /**
     * @param $objectId
     *
     * @return \Mautic\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cloneAction($objectId)
    {
        return $this->cloneStandard($objectId);
    }

    /**
     * @param      $objectId
     * @param bool $ignorePost
     *
     * @return \Mautic\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editAction($objectId, $ignorePost = false)
    {
        return parent::editStandard($objectId, $ignorePost);
    }

    /**
     * @param int $page
     *
     * @return \Mautic\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction($page = 1)
    {
        return $this->indexStandard($page);
    }

    /**
     * @return \Mautic\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction()
    {
        return $this->newStandard();
    }

    protected function getUpdateSelectParams(
        $updateSelect,
        $entity,
        $nameMethod = 'getName',
        $groupMethod = 'getLanguage'
    ) {
        return parent::getUpdateSelectParams($updateSelect, $entity, $nameMethod, '');
    }

    /**
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        //set the page we came from
        $page      = $this->get('session')->get('mautic.recommender.template.page', 1);
        $returnUrl = $this->generateUrl('mautic_recommender_template_index', ['page' => $page]);

        return $this->postActionRedirect(
            [
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'MauticRecommenderBundle:RecommenderTemplate:index',
                'passthroughVars' => [
                    'activeLink'    => '#mautic_recommender_template_index',
                    'mauticContent' => 'recommenderTemplate',
                ],
            ]
        );
    }

    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function deleteAction($objectId)
    {
        return $this->deleteStandard($objectId);
    }

    /**
     * @param $action
     *
     * @return array
     */
    protected function getPostActionRedirectArguments(array $args, $action)
    {
        switch ($action) {
            case 'edit':
                $args['passthroughVars']['closeModal'] = true;
                break;
        }

        return $args;
    }

    /**
     * @param $args
     * @param $action
     *
     * @return mixed
     */
    protected function getViewArguments(array $args, $action)
    {
        /** @var ApiCommands $apiCommands */
        $apiCommands    = $this->get('mautic.recommender.service.api.commands');
        $viewParameters = [];
        switch ($action) {
            case 'new':
            case 'edit':
                $viewParameters['properties'] = $apiCommands->callCommand('ListProperties');
                $viewParameters['settings']   = $this->get('mautic.helper.integration')->getIntegrationObject(
                    'Recommender'
                )->getIntegrationSettings()->getFeatureSettings();
                break;
        }
        $args['viewParameters'] = array_merge($args['viewParameters'], $viewParameters);

        return $args;
    }

    /**
     * @return JsonResponse
     */
    public function processAction()
    {
        if (!$this->get('mautic.security')->isAnonymous()) {
            return new JsonResponse(
                [
                    'success' => 0,
                ]
            );
        }

        /** @var ApiCommands $apiCommands */
        $apiCommands         = $this->get('mautic.recommender.service.api.commands');
        $eventLabel          = $this->get('mautic.helper.core_parameters')->getParameter('eventLabel');
        $integrationSettings = $this->get('mautic.helper.integration')->getIntegrationObject(
            'Recommender'
        )->getIntegrationSettings()->getFeatureSettings();
        $options             = $this->request->request->all();
        $recommender         = $this->request->get('eventDetail');
        $eventDetail         = json_decode(base64_decode($recommender), true);
        $error               = false;

        if (!isset($eventDetail['eventName'])) {
            $error = $this->get('translator')->trans('mautic.plugin.recommender.eventName.not_found', [], 'validators');
        } else {
            if (!empty($integrationSettings['allowedEvents']) && !in_array(
                    $eventDetail['eventName'],
                    array_keys($integrationSettings['allowedEvents'])
                )
            ) {
                $error = $this->get('translator')->trans(
                    'mautic.plugin.recommender.eventName.not_allowed',
                    [
                        '%eventName%' => $eventDetail['eventName'],
                        '%events%'    => implode(', ', array_keys($integrationSettings['allowedEvents'])),
                    ],
                    'validators'
                );
            }
        }

        $response = ['success' => !(bool) $error];
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
