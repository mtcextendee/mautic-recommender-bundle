<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Service;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CookieHelper;
use MauticPlugin\MauticRecommenderBundle\Form\Type\ContactSearchType;
use MauticPlugin\MauticRecommenderBundle\Helper\SqlQuery;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;

class ContactSearch
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var RecommenderClientModel
     */
    private $clientModel;

    /** @var \Symfony\Component\HttpFoundation\ParameterBag */
    private $cookies;

    /**
     * @var CookieHelper
     */
    private $cookieHelper;

    /**
     * @var Container
     */
    private $container;

    /** @var int */
    private $objectId;

    /** @var RecommenderTokenReplacer|object */
    private $recommenderTokenReplacer;

    /**
     * @var RecommenderGenerator|object|Container
     */
    private $recommenderGenerator;

    public function __construct(
        Container $container
    ) {
        $this->container                = $container;
        $this->request                  = $this->container->get('request_stack')->getCurrentRequest();
        $this->clientModel              = $this->container->get('mautic.recommender.model.client');
        $this->cookies                  = $this->request->cookies;
        $this->cookieHelper             = $this->container->get('mautic.helper.cookie');
        $this->recommenderTokenReplacer = $this->container->get('mautic.recommender.service.replacer');
        $this->recommenderGenerator     = $this->container->get('mautic.recommender.service.token.generator');
    }

    /**
     * @return string
     */
    private function getCookieVar()
    {
        return md5('mautic.plugin.recommender.form.example');
    }

    /**
     * @return array
     */
    private function getChoices()
    {
        $leads       = $this->getRepository()->getEntities($this->getEntitiesArgs());
        $leadChoices = [];
        foreach ($leads as $l) {
            $leadChoices[$l->getId()] = $l->getPrimaryIdentifier();
        }

        return $leadChoices;
    }

    public function getForm()
    {
        return $this->container->get('form.factory')->create(
            ContactSearchType::class,
            [
                'search'  => $this->getSearch(),
                'contact' => $this->getContact(),
            ],
            [
                'action'  => $this->getAction(),
                'choices' => array_flip($this->getChoices()),
            ]
        );
    }

    public function getEntitiesArgs()
    {
        $filter           = ['force' => [['column' => 'l.email', 'expr' => 'neq', 'value' => '']]];
        $filter['string'] = $this->getSearch();

        return [
            'limit'          => 25,
            'filter'         => $filter,
            'orderBy'        => 'l.firstname,l.lastname,l.company,l.email',
            'orderByDir'     => 'ASC',
            'withTotalCount' => false,
        ];
    }

    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderForm($objectId, CommonController $controller)
    {
        $this->objectId = $objectId;

        return $controller->renderView(
            'MauticRecommenderBundle:Recommender:example.html.php',
            $this->getViewParameters()
        );
    }

    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function delegateForm($objectId, CommonController $controller)
    {
        $this->objectId = $objectId;

        return $controller->delegateView(
            [
                'viewParameters'  => $this->getViewParameters(),
                'contentTemplate' => 'MauticRecommenderBundle:Recommender:example.html.php',
            ]
        );
    }

    /**
     * @return array
     */
    public function getViewParameters($objectId = null)
    {
        if ($objectId) {
            $this->objectId = $objectId;
        }
        $this->recommenderTokenReplacer->getRecommenderToken()->setUserId($this->getContact());
        $this->recommenderTokenReplacer->getRecommenderToken()->setContent('{recommender='.$this->objectId.'}');
        $this->recommenderTokenReplacer->getRecommenderToken()->setId($this->objectId);

        $content = $this->recommenderGenerator->getContentByToken($this->recommenderTokenReplacer->getRecommenderToken());

        $this->cookieHelper->setCookie(
            $this->getCookieVar(),
            serialize(['search' => $this->getSearch(), 'contact' => $this->getContact()]),
            3600 * 24 * 31 * 365
        );

        return [
            'tmpl'        => $this->getTmpl(),
            'searchValue' => $this->getSearch(),
            'action'      => $this->getAction(),
            'sqlQuery'    => SqlQuery::$query,
            'cnt'         => $content,
            'form'        => $this->getForm()->createView(),
            'recommender' => $this->recommenderTokenReplacer->getRecommenderToken()->getRecommender(),
            'contactId'   => $this->getContact(),
        ];
    }

    /**
     * Get action of url for example.
     *
     * @return string
     */
    private function getAction()
    {
        return $this->container->get('router')->generate(
            'mautic_recommender_action',
            ['objectAction' => 'example', 'objectId' => $this->objectId]
        );
    }

    /**
     * Get unserialized content from cookie.
     *
     * @param string|null $key
     *
     * @return array|string
     */
    private function getSavedData($key = null)
    {
        $savedData = unserialize($this->cookies->get($this->getCookieVar()));
        if (!is_array($savedData)) {
            $savedData = [];
        }
        if ($key) {
            return isset($savedData[$key]) ? $savedData[$key] : '';
        }

        return $savedData;
    }

    /**
     * Get search.
     *
     * @return string
     */
    private function getSearch()
    {
        return $this->request->get('search', $this->getSavedData('search'));
    }

    /**
     * Get contact ID from form.
     *
     * @return string
     */
    private function getContact()
    {
        return isset($this->request->get('contact_search')['contact']) ? $this->request->get(
            'contact_search'
        )['contact'] : $this->getSavedData('contact');
    }

    /**
     * Get template type.
     *
     * @return string
     */
    private function getTmpl()
    {
        return $this->request->get('tmpl', 'index');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\Mautic\LeadBundle\Entity\LeadRepository
     */
    private function getRepository()
    {
        return $this->clientModel->getContactRepository();
    }
}
