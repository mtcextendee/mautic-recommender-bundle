<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener;

use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Model\ListModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use Symfony\Component\Translation\TranslatorInterface;

class Choices
{
    const ALLOWED_TABLES = ['recommender_event_log', 'recommender_event_log_property_value'];

    /**
     * @var Fields
     */
    private $fields;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * SegmentChoices constructor.
     *
     * @param Fields              $fields
     * @param ListModel           $listModel
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Fields $fields,
        ListModel $listModel,
        TranslatorInterface $translator,
        IntegrationHelper $integrationHelper
    ) {
        $this->fields            = $fields;
        $this->listModel         = $listModel;
        $this->translator        = $translator;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * @param LeadListFiltersChoicesEvent $event
     * @param                             $object
     */
    public function addChoices(LeadListFiltersChoicesEvent $event, $object)
    {
        $integration = $this->integrationHelper->getIntegrationObject('Recommender');
        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

        $choices = $this->getChoices();
        foreach (self::ALLOWED_TABLES as $table) {
            foreach ($choices[$table] as $key=>$options) {
                $event->addChoice($table, $key, $options);
            }
        }
    }

    private function getChoices()
    {
        $choices = [];
        foreach (self::ALLOWED_TABLES as $table) {
            $fields = $this->fields->getFields($table);
            foreach ($fields as $key => $field) {
                $properties = [];
                if (isset($field['properties'])) {
                    $properties = $field['properties'];
                } elseif (isset($field['type'])) {
                    $properties['type'] = $field['type'];
                }

                $choices[$table][$key] = [
                    'label'      => $this->translator->trans('mautic.plugin.recommender.form.event').' '.$this->translator->trans($field['name']),
                    'properties' => $properties,
                    'operators'  => $this->listModel->getOperatorsForFieldType(
                        $properties['type']
                    ),
                ];
            }
        }

        return $choices;
    }
}
