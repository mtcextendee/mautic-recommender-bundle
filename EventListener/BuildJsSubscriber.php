<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class BuildJsSubscriber implements EventSubscriberInterface
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BuildJsSubscriber constructor.
     */
    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        IntegrationHelper $integrationHelper,
        RouterInterface $router
    ) {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->integrationHelper    = $integrationHelper;
        $this->router               = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => [
                ['onBuildJsTop', 300],
                ['onDynamicRecommenderJs', 400],
            ],
        ];
    }

    public function onBuildJsTop(BuildJsEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || false === $integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }

        $url        = $this->router->generate('mautic_recommender_send_event', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $eventLabel = $this->coreParametersHelper->get('eventLabel');
        //basic js
        $js = <<<JS
        
    MauticJS.recommenderEvent = function (params) {
        var requestParams = {};
        var queue = [];
        var eventParams = {};
        
        if (!params){
            if (typeof MauticJS.getInput === 'function') {
                queue = MauticJS.getInput('send', '{$eventLabel}');
            } else {
                return false;
            }
        }else{
            queue.push(params);
        }
        
        if (queue) {
            for (var i=0; i<queue.length; i++) {
                var event = queue[i];
                //Even further ensure event context
                if (event[0] == 'send' && event[1] == '{$eventLabel}'){
                    // Merge user defined tracking pixel parameters.
                    if (typeof event[2] === 'object') {
                        for (var attr in event[2]) {
                            eventParams[attr] = event[2][attr];
                        }
                        requestParams['eventDetail'] = btoa(JSON.stringify(eventParams));
                        requestParams['params'] = btoa(unescape(encodeURIComponent(JSON.stringify(event))));
                    }
                    MauticJS.makeCORSRequest('POST', '{$url}', requestParams, 
                        function(response) {
                        },
                        function() {
                            
                    });
                }
            }
        }
    }
    
    MauticJS.onFirstEventDelivery(function() {
         // Process events right after mtc.js loaded
        MauticJS.recommenderEvent();
    });

    // Process events after new are added
    document.addEventListener('eventAddedToMauticQueue', function(e) {
      if(e.detail[0] == 'send' && e.detail[1] == '{$eventLabel}'){
         MauticJS.recommenderEvent(e.detail);
      }
    });
        
JS;
        $event->appendJs($js, 'RecommenderTemplate');
    }

    public function onDynamicRecommenderJs(BuildJsEvent $event)
    {
        $recommenderUrl = $this->router->generate('mautic_recommender_dwc', ['objectId' => 'slotNamePlaceholder'], UrlGeneratorInterface::ABSOLUTE_URL);

        $js = <<<JS
        
MauticJS.replaceRecommender = function (params) {
    params = params || {};

    var recommenderSlots = document.querySelectorAll('[data-slot="recommender"]');
    if (recommenderSlots.length) {
        MauticJS.iterateCollection(recommenderSlots)(function(node, i) {
            var recommenderId = node.dataset['recommenderId'];
            if ('undefined' === typeof recommenderId) {
                node.innerHTML = '';
                return;
            }
            
            var url = '{$recommenderUrl}'.replace('slotNamePlaceholder', recommenderId);
            
           var recommenderFilterTokens = node.dataset['filterTokens'];
           if ('undefined' !== typeof recommenderFilterTokens) {
               params['filterTokens'] = btoa(recommenderFilterTokens);
           }

            MauticJS.makeCORSRequest('GET', url, params, function(response, xhr) {
                
                if (response.content) {
                    var recommenderContent = response.content;
                    node.innerHTML = recommenderContent;
                    
                    if (response.id && response.sid) {
                        MauticJS.setTrackedContact(response);
                    }
               
                }
            });
        });
    }
};

MauticJS.beforeFirstEventDelivery(MauticJS.replaceRecommender);
JS;
        $event->appendJs($js, 'Mautic Recommender Content');
    }
}
