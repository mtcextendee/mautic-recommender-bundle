<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Controller\AjaxLookupControllerTrait;
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTableOrderType;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AjaxController extends CommonAjaxController
{
    use AjaxLookupControllerTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function generatePreviewAction(Request $request)
    {
        $data        = [];
        $recommender = $request->request->all();

        if (isset($recommender['recommender_templates'])) {
            $recommenderEntity = new RecommenderTemplate();
            $recommenderArrays = InputHelper::_($recommender['recommender_templates']);
            foreach ($recommenderArrays as $key=>$recommenderArray) {
                $setter = 'set'.ucfirst($key);
                if (method_exists($recommenderEntity, $setter)) {
                    $recommenderEntity->$setter($recommenderArray);
                }
            }
            $data['content'] = $this->get('mautic.helper.templating')->getTemplating()->render(
                'MauticRecommenderBundle:Builder\\Page:generator.html.php',
                [
                    'recommender'  => $recommenderEntity,
                    'settings'     => $this->get('mautic.helper.integration')->getIntegrationObject('Recommender')->getIntegrationSettings()->getFeatureSettings(),
                    'preview'      => true,
                ]
            );
        }

        return $this->sendJsonResponse($data);
    }

    public function dwcAction(Request $request)
    {
        /** @var RecommenderModel $recommenderModel */
        $recommenderModel = $this->getModel('recommender.recommender');
        if (!$recommender = $recommenderModel->getEntity($request->get('objectId'))) {
            return $this->notFound();
        }

        /** @var RecommenderToken $recommenderToken */
        $recommenderToken = $this->get('mautic.recommender.service.token');
        $recommenderToken->setRecommender($recommender);
        $recommenderToken->setId($request->get('objectId'));
        if ($request->get('filterTokens')) {
            $filterTokens = json_decode(base64_decode($request->get('filterTokens')), true);
            if (is_array($filterTokens)) {
                foreach ($filterTokens as $token=>$replace) {
                    $recommenderToken->addFilterToken($token, $replace);
                }
            }
        }

        $recommenderTokenReplace = $this->get('mautic.recommender.service.token.generator');

        return $this->sendJsonResponse(
            [
                'success' => 1,
                'content' => $recommenderTokenReplace->getContentByToken($recommenderToken),
            ]
        );
    }

    public function listavailablefunctionsAction(Request $request)
    {
        $column = $request->request->get('column', $request->query->get('column'));
        //$tableOrderForm = $this->get();
        $fields = $this->get('mautic.recommender.filter.fields.recommender')->getSelectOptions();

        $form = $this->get('form.factory')->createNamedBuilder(
            'recommender',
            'form',
            null,
            ['auto_initialize' => false]
        )->add(
            'tableOrder',
            RecommenderTableOrderType::class,
            ['data' => ['column' => $column], 'fields' => $fields]
        )->getForm();

        $data['content'] = $this->get('mautic.helper.templating')->getTemplating()->render(
            'MauticRecommenderBundle:Recommender:form.function.html.php',
            [
                'form'  => $form->createView(),
            ]
        );

        return $this->sendJsonResponse($data);
    }
}
