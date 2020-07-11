<?php

namespace MauticPlugin\MauticRecommenderBundle\Command;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiUserItemsInteractions;
use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Events\Processor;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushTestDataToRecommenderCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mautic:recommender:import:testdata')
            ->setDescription('Import test data to Recommender');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create events first
        /** @var RecommenderEventModel $recommenderEventModel */
        $recommenderEventModel = $this->getContainer()->get('mautic.recommender.model.event');
        $eventNames            = [2=>'DetailView', 3=>'Wishlist', 5=>'addToCart', 10=>'Purchase'];
        foreach ($eventNames as $weight=>$eventName) {
            if (!$entity = $recommenderEventModel->getRepository()->findBy(['name' => $eventName])) {
                $event = new Event();
                $event->setName($eventName);
                $event->setWeight($weight);
                $recommenderEventModel->saveEntity($event);
            }
        }

        // Import items first
        /** @var ApiCommands $apiCommands */
        $apiCommands = $this->getContainer()->get('mautic.recommender.service.api.commands');
        $items       = \JsonMachine\JsonMachine::fromFile(__DIR__.'/data/items.json');
        $apiCommands->importItems($items, 1000, RecommenderIntegration::IMPORT_TIMEOUT, $output);

        // then import events
        $items = \JsonMachine\JsonMachine::fromFile(__DIR__.'/data/events.json');
        /** @var Processor $eventProcessor */
        $eventProcessor = $this->getContainer()->get('mautic.recommender.events.processor');
        $counter        = 0;
        foreach ($items as $item) {
            try {
                $eventProcessor->process($item);
                ++$counter;
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }
        }
        $output->writeln('');
        $output->writeln('Imported '.$counter.' events');
    }
}
