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
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $page      = $this->get('session')->get('mautic.recommender.page', 1);
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
     * @param      $entity
     * @param Form $form
     * @param      $action
     * @param      $isPost
     * @param null $objectId
     * @param bool $isClone
     */
    protected function beforeFormProcessed($entity, Form $form, $action, $isPost, $objectId = null, $isClone = false)
    {
        $this->setFormTheme($form, 'MauticRecommenderBundle:Recommender:form.html.php', 'MauticRecommenderBundle:FormTheme\Filter');
    }

    /**
     * @param $args
     * @param $action
     *
     * @return mixed
     */
    protected function getViewArguments(array $args, $action)
    {
        /** @var RecommenderTokenReplacer $recommenderTokenReplacer */
        $recommenderTokenReplacer    = $this->get('mautic.recommender.service.replacer');
        /** @var LeadModel $leadModel */
        $leadModel    = $this->get('mautic.lead.model.lead');
        $viewParameters = [];
        switch ($action) {
            case 'edit':
                $filter = [
                    'force'  => [['column' => 'l.email', 'expr' => 'eq', 'value' => 'zdeno@kuzmany.biz']],
                    'limit'=>1,
                    'orderBy'    => 'l.id',
                    'orderByDir' => 'desc',
                    'withTotalCount' => false,
                ];
                $leads = $leadModel->getEntities($filter);
                $lead = reset($leads);
                $recommenderTokenReplacer->getRecommenderToken()->setUserId($lead->getId());
                $recommenderTokenReplacer->getRecommenderToken()->setContent('test {recommender=3}');
                $content = $recommenderTokenReplacer->getReplacedContent();
                $viewParameters['sqlQuery'] = SqlQuery::$query;
                break;
        }
        $args['viewParameters'] = array_merge($args['viewParameters'], $viewParameters);

        return $args;
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
