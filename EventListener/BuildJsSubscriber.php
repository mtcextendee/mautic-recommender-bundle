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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class BuildJsSubscriber.
 */
class BuildJsSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => [
                ['onBuildJsTop', 300],
            ],
        ];
    }

    /**
     * @param BuildJsEvent $event
     */
    public function onBuildJsTop(BuildJsEvent $event)
    {
        $url = $this->router->generate('mautic_recommender_process_action', [], UrlGeneratorInterface::ABSOLUTE_URL);

        //basic js
        $js = <<<JS
        
        MauticJS.createRecommenderRequestFromNode = function(onLoadPixel) {
         var actions = [];
        // Add a custom data attribute to all videos
        MauticJS.iterateCollection(onLoadPixel)(function(node, i) {
            var options = [];
            console.log(node.dataset.component);
            if(node.dataset.component && node.dataset.itemId){
                 var a = {}; 
                 Object.keys(node.dataset).map(function(key){ a[key] = node.dataset[key];});
                 actions.push(JSON.stringify(a));
            }else{
                MauticJS.log('data-recombe-action or data-recombe-item-id missing');
            }
        });
        var data = [];
        data['recommender'] = btoa(JSON.stringify(actions));
          MauticJS.makeCORSRequest('GET', '{$url}', data, function(response, xhr) {
                 console.log(response);
            });
        }
        
        var onLoadPixel = document.getElementsByClassName('recommender-pixel');
        if(onLoadPixel.length){
            MauticJS.createRecommenderRequestFromNode(onLoadPixel);
        }
        
        [].forEach.call( document.querySelectorAll( '.recommender-pixel' ), function ( node ) {
            var elementsToProcess = [];
            if(node.dataset.event){
                var event = node.dataset.event;
                //remove from element
                delete node.dataset.event;
                if( event == 'load')
                    {
                        elementsToProcess.push(node);
                    }else if(event == 'click'){
                              node.addEventListener( 'click', function (e) {
               e.preventDefault();
               console.log(node.dataset.component);
               console.log((e.target).dataset.component);
           //MauticJS.createRecommenderRequestFromNode(node);
        }, false );
        });                 
                    }else if(event == 'submit'){
                        
                    }
                
            }
            
           
        
       

JS;
        $event->appendJs($js, 'Recommender');
    }


}
