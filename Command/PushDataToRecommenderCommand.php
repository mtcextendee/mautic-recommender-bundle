<?php

namespace MauticPlugin\MauticRecommenderBundle\Command;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Entity\IntegrationEntity;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Model\IntegrationEntityModel;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiUserItemsInteractions;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushDataToRecommenderCommand extends ContainerAwareCommand
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var array
     */
    private $types = ['contacts', 'items'];

    /**
     * @var array
     */
    //private $actions = ['views', 'carts', 'purchases', 'bookmarks', 'ratings'];
    private $actions = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mautic:recommender:import')
            ->setDescription('Import data to RecommenderTemplate')
            ->addOption(
                '--type',
                '-t',
                InputOption::VALUE_REQUIRED,
                'Type options: '.implode(', ', $this->getTypes()),
                null
            )->addOption(
                '--file',
                '-f',
                InputOption::VALUE_OPTIONAL,
                'JSON file to import for types for '.implode(', ', $this->getActions())
            );
        $this->addOption('--batch-limit', '-l', InputOption::VALUE_OPTIONAL, 'Set batch size of contacts to process per round. Defaults to 50.', 50);


        parent::configure();
    }

    /**
     * @param $date
     * @param $integrationEntityId
     * @param $internalEntityId
     * @param $integrationEntityName
     * @param $internalEntityName
     *
     * @return IntegrationEntity
     */
    private function createIntegrationEntity($date, $integrationName, $integrationEntityName, $internalEntityName)
    {
        $integrationEntity = new IntegrationEntity();
        $integrationEntity->setDateAdded($date);
        $integrationEntity->setLastSyncDate($date);
        $integrationEntity->setIntegration($integrationName);
        $integrationEntity->setIntegrationEntity($integrationEntityName);
        $integrationEntity->setInternalEntity($internalEntityName);

        return $integrationEntity;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IntegrationHelper $integrationHelper */
        $integrationHelper = $this->getContainer()->get('mautic.helper.integration');
        $integrationObject = $integrationHelper->getIntegrationObject('Recommender');
        /** @var Translator $translator */
        $translator = $this->getContainer()->get('translator');

        if (!$integrationObject->getIntegrationSettings()->getIsPublished()) {
            return $output->writeln('<info>'.$translator->trans('mautic.plugin.recommender.disabled').'</info>');
        }

        /** @var RecommenderHelper $recommenderHelper */
        $recommenderHelper = $this->getContainer()->get('mautic.recommender.helper');

        $type = $input->getOption('type');

        if (empty($type)) {
            return $output->writeln(
                sprintf(
                    '<error>ERROR:</error> <info>'.$translator->trans(
                        'mautic.plugin.recommender.command.type.required',
                        ['%types' => implode(', ', $this->getTypes())]
                    ).'</info>'
                )
            );
        }

        if (!in_array($type, $this->getTypes())) {
            return $output->writeln(
                sprintf(
                    '<error>ERROR:</error> <info>'.$translator->trans(
                        'mautic.plugin.recommender.command.bad.type',
                        ['%type' => $type, '%types' => implode(', ', $this->getTypes())]
                    ).'</info>'
                )
            );
        }

        $file = $input->getOption('file');


        if (!in_array($type, $this->getTypes()) && empty($file)) {
            return $output->writeln(
                sprintf(
                    '<error>ERROR:</error> <info>'.$translator->trans(
                        'mautic.plugin.recommender.command.option.required',
                        ['%file' => 'file', '%actions' => implode(', ', $this->getActions())]
                    )
                )
            );
        }

        if ($type !== 'contacts') {
            if (empty($file)) {
                return $output->writeln(
                    sprintf(
                        '<error>ERROR:</error> <info>'.$translator->trans(
                            'mautic.plugin.recommender.command.file.required'
                        )
                    )
                );
            }

            if (empty(!file_exists($file))) {
                return $output->writeln(
                    sprintf(
                        '<error>ERROR:</error> <info>'.$translator->trans(
                            'mautic.plugin.recommender.command.file.fail',
                            ['%file' => $file]
                        )
                    )
                );
            }
            $items = \JsonMachine\JsonMachine::fromFile($file);

            if (empty($items) || ![$items]) {
                return $output->writeln(
                    sprintf(
                        '<error>ERROR:</error> <info>'.$translator->trans(
                            'mautic.plugin.recommender.command.json.fail',
                            ['%file' => $file]
                        )
                    )
                );
            }
        }

        // import Leads
        $criteria['integration']       = 'RecommenderTemplate';
        $criteria['integrationEntity'] = 'users';
        $criteria['internalEntity']    = 'contacts';
        //$integrationEntity = $em->getRepository(IntegrationEntity::class)->findOneBy($criteria);
        /** @var ApiCommands $apiCommands */
        $apiCommands = $this->getContainer()->get('mautic.recommender.service.api.commands');
        /** @var IntegrationEntityModel $integrationEntityModel */
        $integrationEntityModel = $this->getContainer()->get('mautic.plugin.model.integration_entity');
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        switch ($type) {
            case "items":
                $apiCommands->ImportItems($items, $input->getOption('batch-limit'), $output);
                break;
        }

        $requestsPropertyValues = [];
        switch ($type) {
            case "views":
                $apiCommands->callCommand('AddDetailView', $items);
                break;

            case "purchases":
                $apiCommands->callCommand('AddPurchase', $items);
                break;

            case "carts":
                $apiCommands->callCommand('AddCartAddition', $items);
                break;

            case "bookmarks":
                $apiCommands->callCommand('AddBookmark', $items);
                break;
            case "ratings":
                $apiCommands->callCommand('AddRating', $items);
                break;
            case "portions":
                $apiCommands->callCommłand('SetViewPortion', $items);
                break;
        }

        if ($apiCommands->hasCommandOutput()) {
            $this->displayCmdTextFromResult(
                $apiCommands->getCommandOutput(),
                'user property values',
                $output
            );
        }
    }


    /**
     * Display commands results
     *
     * @param array  $results
     * @param string $title
     */
    private function displayCmdTextFromResult(array $results, $title = '', OutputInterface $output)
    {
        $errors = [];
        foreach ($results as $result) {
            if (!empty($result['json']['error'])) {
                $errors[] = $result['json']['error'];
            }
        }
        // just add empty space
        if ($title != '') {
            $title .= ' ';
        }
        $errors = [];
        $output->writeln(sprintf('<info>Procesed '.$title.count($results).'</info>'));
        $output->writeln('Success '.$title.(count($results) - count($errors)));
        /*if (!empty($errors)) {
            $output->writeln('Errors '.$title.count($errors));
            $output->writeln($errors, true);
        }*/
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return array_merge($this->types, $this->actions);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
