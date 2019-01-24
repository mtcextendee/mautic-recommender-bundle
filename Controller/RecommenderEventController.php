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

    const NUM = 100;
    const PROBABILITY_PURCHASED = 0.1;

    /**
     * @return \Mautic\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction()
    {
# Prepare requests for setting a catalog of computers
        $requests = array();
        for($i=0; $i<self::NUM; $i++)
        {
            $itemId = "computer-{$i}";
            $r = new Reqs\SetItemValues(
                $itemId,
                //values:
                [
                    'price' => rand(15000, 25000),
                    'num-cores' => rand(1, 8),
                    'description' => 'Great computer',
                    'in_stock_from' => new DateTime('NOW'),
                    'image' => "http://examplesite.com/products/{$itemId}.jpg"
                ],
                //optional parameters:
                ['cascadeCreate' => true] // Use cascadeCreate for creating item
            // with given itemId, if it doesn't exist]
            );
            array_push($requests, $r);
        }

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
}
