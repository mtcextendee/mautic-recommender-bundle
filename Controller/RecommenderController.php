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
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Event\PageDisplayEvent;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel;
use Recommender\RecommApi\Requests as Reqs;
use Recommender\RecommApi\Exceptions as Ex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecommenderController extends AbstractStandardFormController
{

    /**
     * {@inheritdoc}
     */
    protected function getJsLoadMethodPrefix()
    {
        return 'recommender';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'recommender.recommender';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteBase()
    {
        return 'recommender';
    }

    /***
     * @param null $objectId
     *
     * @return string
     */
    protected function getSessionBase($objectId = null)
    {
        return 'recommender'.(($objectId) ? '.'.$objectId : '');
    }

    /**
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticRecommenderBundle:Recommender';
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

    /**
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        //set the page we came from
        $page = $this->get('session')->get('mautic.recommender.page', 1);
        $returnUrl = $this->generateUrl('mautic_recommender_index', ['page' => $page]);

        return $this->postActionRedirect(
            [
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'MauticRecommenderBundle:Recommender:index',
                'passthroughVars' => [
                    'activeLink'    => '#mautic_recommender_index',
                    'mauticContent' => 'recommender',
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
     * @param $args
     * @param $action
     *
     * @return mixed
     */
    protected function getViewArguments(array $args, $action)
    {
        /** @var ApiCommands $apiCommands */
        $apiCommands    = $this->get('mautic.recommender.service.api.commands');
        $integration    = $this->get('mautic.integration.recommender');
        $viewParameters = [];
        switch ($action) {
            case 'new':
            case 'edit':
                $viewParameters['properties'] = $apiCommands->callCommand('ListItemProperties');
                $viewParameters['settings']   = $integration->getIntegrationSettings()->getFeatureSettings();
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
        $apiCommands = $this->get('mautic.recommender.service.api.commands');
        /** @var LeadModel $leadModel */
        $leadModel = $this->get('mautic.lead.model.lead');
        $lead      = $leadModel->getCurrentLead();


        /** @var ContactTracker $contactTracker */
        //$contactTracker = $this->get('mautic.tracker.contact');
        $options           = $this->request->request->all();

        $recommender = $this->request->get('recommender');
        $requests = json_decode(base64_decode($recommender), true);
        $response = [];
        foreach ($requests as $request) {
            $request = json_decode($request, true);
            if (!is_array($request) || !isset($request['component'])) {
                continue;
            }
            $component = $request['component'];
            $request['userId'] = $lead->getId();
            unset($request['component']);
            $apiCommands->callCommand($component, $request);
            $response[] = $apiCommands->getCommandOutput();
        }
        return new JsonResponse(
            [
                'response' => $response,
            ]
        );
    }
}
