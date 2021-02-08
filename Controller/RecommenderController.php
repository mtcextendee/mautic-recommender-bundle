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
use MauticPlugin\MauticRecommenderBundle\Events\Processor;
use MauticPlugin\MauticRecommenderBundle\Service\ContactSearch;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Monolog\Logger;
use Symfony\Component\Form\Form;
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
        $returnUrl = $this->generateUrl(
            'mautic_recommender_action',
            ['objectId' => $objectId, 'objectAction' => 'edit']
        );

        return $this->postActionRedirect(
            [
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => 1],
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
        $viewParameters              = [];
        switch ($action) {
            case 'edit':
                /** @var ContactSearch $contactSearch */
                $featureSettings = $this->get('mautic.helper.integration')->getIntegrationObject(
                    'Recommender'
                )->getIntegrationSettings()->getFeatureSettings();
                if (!empty($featureSettings['show_recommender_testbench'])) {
                    //$viewParameters['tester'] = $this->get('mautic.recommender.contact.search')->renderForm($args['objectId'], $this);
                    $viewParameters['tester'] = $this->renderView('MauticRecommenderBundle:Recommender:example.html.php', $this->get('mautic.recommender.contact.search')->getViewParameters($args['entity']->getId()));
                }
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
        /** @var Logger $logger */
        $logger      = $this->get('monolog.logger.mautic');
        $recommender = $this->request->get('eventDetail');
        $eventDetail = json_decode(base64_decode($recommender), true);
        $params      = $this->request->get('params');

        /** @var Processor $eventProcessor */
        $eventProcessor = $this->get('mautic.recommender.events.processor');
        if (empty($eventDetail)) {
            $logger->log('error', 'Empty event details from pixel event: '.$recommender.' with params '.$params);
        }

        try {
            $eventProcessor->process($eventDetail);

            return new JsonResponse(
                [
                    'success' => 1,
                ]
            );
        } catch (\Exception $e) {
            $logger->log('debug', $e->getMessage().' with params '.$params);
            $error = $e->getMessage();

            return new JsonResponse(
                [
                    'success' => 0,
                    'error'   => $error,
                ]
            );
        }
    }

    public function exampleAction($objectId)
    {
        return $this->get('mautic.recommender.contact.search')->delegateForm($objectId, $this);
    }
}
