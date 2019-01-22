<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Controller\Api;

use FOS\RestBundle\Util\Codes;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use Recommender\RecommApi\Exceptions as Ex;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class RecommenderApiController.
 */
class RecommenderApiController extends CommonApiController
{
    /**
     * @var RecommenderHelper
     */
    protected $recommenderHelper;

    /**
     * @param FilterControllerEvent $event
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->leadModel         = $this->getModel('lead.lead');
        $this->recommenderHelper = $this->container->get('mautic.recommender.helper');
        parent::initialize($event);
    }


    /**
     * @param $component
     *
     * @return array|Response
     */
    public function processAction($component)
    {
        die();
        $data = $this->request->request->all();
        /** @var ApiCommands $apiCommands */
        $apiCommands = $this->get('mautic.recommender.service.api.commands');
        $apiCommands->callCommand($component, $this->request->request->all());
        $view = $this->view(['succes' => true]);
//        return $this->handleView($view);

        return $this->returnError(
            $this->translator->trans(
                'mautic.plugin.recommender.api.component.error',
                ['%componet' => $component],
                'validators'
            ),
            Response::HTTP_BAD_REQUEST
        );
    }
}
