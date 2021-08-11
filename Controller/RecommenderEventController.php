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
use MauticPlugin\MauticRecommenderBundle\Api\Client\Request\RecommenderEvent;
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use MauticPlugin\MauticRecommenderBundle\Service\ContactSearch;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

class RecommenderEventController extends AbstractStandardFormController
{
    /**
     * {@inheritdoc}
     */
    protected function getJsLoadMethodPrefix()
    {
        return 'recommenderEvent';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'recommender.event';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteBase()
    {
        return 'recommender_event';
    }

    /***
     * @param null $objectId
     *
     * @return string
     */
    protected function getSessionBase($objectId = null)
    {
        return 'recommenderEvent'.(($objectId) ? '.'.$objectId : '');
    }

    /**
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticRecommenderBundle:RecommenderEvent';
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
        $page      = $this->get('session')->get('mautic.recommender.event.page', 1);
        $returnUrl = $this->generateUrl('mautic_recommender_event_index', ['page' => $page]);

        return $this->postActionRedirect(
            [
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'MauticRecommenderBundle:RecommenderEvent:index',
                'passthroughVars' => [
                    'activeLink'    => '#mautic_recommender_event_index',
                    'mauticContent' => 'recommenderEvent',
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

    protected function getDefaultOrderColumn()
    {
        return 'weight';
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
        $viewParameters              = [];
        switch ($action) {
            case 'index':
                /** @var RecommenderEventModel $recommenderEventModel */
                $recommenderEventModel = $this->get('mautic.recommender.model.event');
                /** @var Event $event */
                foreach ($args['viewParameters']['items'] as $event) {
                    $event->setNumberOfLogs($recommenderEventModel->getRepository()->getEventsCount($event->getId()));
                    $event->setLastDateAdded($recommenderEventModel->getRepository()->getEventLastDate($event->getId()));
                }
                break;
        }
        $args['viewParameters'] = array_merge($args['viewParameters'], $viewParameters);

        return $args;
    }
}
