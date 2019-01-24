<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MauticPlugin\MauticRecommenderBundle\Api\RecommenderApi;
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Events\Processor;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadItemsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var RecommenderApi $recommenderApi */
        $recommenderApi = $this->container->get('mautic.recommender.api.recommender');
        /** @var Processor $eventProcessor */
        $eventProcessor = $this->getContainer()->get('mautic.recommender.events.processor');
        /** @var RecommenderEventModel $recommenderEventModel */
        $recommenderEventModel = $this->getContainer()->get('mautic.recommender.model.event');
        $eventNames = [2=>'DetailView', 5=>'addToCart', 10=>'Purchase'];
        foreach ($eventNames as $weight=>$eventName) {
            if (!$entity = $recommenderEventModel->getRepository()->findBy(['name' => $eventName])) {
                $event = new Event();
                $event->setName($eventName);
                $event->setWeight($weight);
                $recommenderEventModel->saveEntity($event);
            };
        }
        $items = \JsonMachine\JsonMachine::fromFile(__DIR__.'/items.json');
        $i = 0;

        try {
            foreach ($items as $item) {
                $i += $recommenderApi->getClient()->send(
                    'ImportItems',
                    $item,
                    ['timeout' => '-1 day']
                );
            }
        } catch (\Exception $e) {

        }

        $events = \JsonMachine\JsonMachine::fromFile(__DIR__.'/events.json');

        foreach ($events as $event) {

            try {
                $eventProcessor->process($event);
            } catch (\Exception $e) {
            }

        }

    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 9;
    }
}
