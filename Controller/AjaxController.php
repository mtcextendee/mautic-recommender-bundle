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
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticFocusBundle\Model\FocusModel;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

use MauticPlugin\MauticRecommenderBundle\Form\Type\RecommenderTableOrderType;

class AjaxController extends CommonAjaxController
{

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function generatePreviewAction(Request $request)
    {
        $data  = [];
        $recommender = $request->request->all();

        if (isset($recommender['recommender_templates'])) {
            $recommenderEntity = new RecommenderTemplate();
            $accessor = PropertyAccess::createPropertyAccessor();
            $recommenderArrays = InputHelper::_($recommender['recommender_templates']);
            foreach ($recommenderArrays as $key=>$recommenderArray) {
             //   $accessor->setValue($recommenderEntity, $key, $recommenderArray);
                $setter = 'set'.ucfirst($key);
                if (method_exists($recommenderEntity, $setter)) {
                    $recommenderEntity->$setter($recommenderArray);
                }
            }
            $data['content'] = $this->get('mautic.helper.templating')->getTemplating()->render(
                'MauticRecommenderBundle:Builder\\Page:generator.html.php',
                [
                    'recommender'  => $recommenderEntity,
                    'settings'  => $this->get('mautic.helper.integration')->getIntegrationObject('Recommender')->getIntegrationSettings()->getFeatureSettings(),
                    'preview' => true,
                ]
            );
        }

        return $this->sendJsonResponse($data);
    } 

    public function listavailablefunctionsAction(Request $request)
    {
        $column = $request->request->get('column');
        //$tableOrderForm = $this->get();
        $form = $this->createForm(RecommenderTableOrderType::class, array('data' => $column));
        //return $this->get('mautic.recommender.contact.search')->delegateForm($objectId, $this);
        
        $data['content'] = $this->get('mautic.helper.templating')->getTemplating()->render(
            'MauticRecommenderBundle:Recommender:form.function.html.php',
            [
                'form'  => $form->createView(),                    
            ]
        );

        return $this->sendJsonResponse($data);
    }       
}
