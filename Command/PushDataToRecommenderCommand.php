<?php

namespace MauticPlugin\MauticRecommenderBundle\Command;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiUserItemsInteractions;
use MauticPlugin\MauticRecommenderBundle\Events\Processor;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Integration\RecommenderIntegration;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushDataToRecommenderCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    private $types   = ['events', 'items'];
    private $actions = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mautic:recommender:import')
            ->setDescription('Import data to Recommender')
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
        $this->addOption(
            '--batch-limit',
            '-l',
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Set batch size of contacts to process per round. Defaults to %s.',
                RecommenderIntegration::IMPORT_BATCH
            )
        );

        $this->addOption(
            '--timeout',
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf('Set delay to ignore item to update. Default %s.', RecommenderIntegration::IMPORT_TIMEOUT)
        );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IntegrationHelper $integrationHelper */
        $integrationHelper      = $this->getContainer()->get('mautic.helper.integration');
        $integrationObject      = $integrationHelper->getIntegrationObject('Recommender');
        $integrationSettings    = $integrationObject->getIntegrationSettings();
        $featureSettings        = $integrationSettings->getFeatureSettings();

        /** @var Translator $translator */
        $translator = $this->getContainer()->get('translator');

        if (!$integrationSettings->getIsPublished()) {
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

        if (!empty($input->getOption('file'))) {
            $file = $input->getOption('file');
        } else {
            switch ($type) {
                case 'items':
                    if (!empty($featureSettings['items_import_url'])) {
                        $file = $featureSettings['items_import_url'];
                    }
                    break;
                case 'events':
                    if (!empty($featureSettings['events_import_url'])) {
                        $file = $featureSettings['events_import_url'];
                    }
                    break;
            }
        }

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

        if ('contacts' !== $type) {
            if (empty($file)) {
                return $output->writeln(
                    sprintf(
                        '<error>ERROR:</error> <info>'.$translator->trans(
                            'mautic.plugin.recommender.command.file.required'
                        )
                    )
                );
            }

            if (!empty($input->getOption('batch-limit')) && intval($input->getOption('batch-limit'))) {
                $batchLimit = intval($input->getOption('batch-limit'));
            } elseif (!empty($featureSettings['batch_limit']) && intval($featureSettings['batch_limit'])) {
                $batchLimit = intval($featureSettings['batch_limit']);
            } else {
                $batchLimit = RecommenderIntegration::IMPORT_BATCH;
            }

            $data = $this->getContentFromUrl($file);

            $items = \JsonMachine\JsonMachine::fromString($data);

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

        if (!empty($input->getOption('timeout'))) {
            $timeout = $input->getOption('timeout');
        } elseif (!empty($featureSettings['timeout'])) {
            $timeout = $featureSettings['timeout'];
        } else {
            $timeout = RecommenderIntegration::IMPORT_TIMEOUT;
        }

        /** @var ApiCommands $apiCommands */
        $apiCommands = $this->getContainer()->get('mautic.recommender.service.api.commands');

        switch ($type) {
            case 'items':
                $apiCommands->importItems($items, $batchLimit, $timeout, $output);
                $items = \JsonMachine\JsonMachine::fromString($data);
                $apiCommands->deactivateMissingItems($items, $output);
                break;
            case 'events':
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
                $output->writeln('Imported '.$counter.' events');
                break;
        }
    }

    /**
     * @param $url
     *
     * @return bool|string
     */
    private function getContentFromUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
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
