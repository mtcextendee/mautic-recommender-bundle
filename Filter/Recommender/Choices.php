<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Recommender;

use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use Symfony\Component\Translation\TranslatorInterface;

class Choices
{
    const ALLOWED_TABLES = ['recommenders', 'recommender_event_log', 'recommender_event_log_property_value', 'recommender_item', 'recommender_item_property_value'];

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

    private $fieldChoices = [];

    /**
     * SegmentChoices constructor.
     */
    public function __construct(Fields $fields, ListModel $listModel, TranslatorInterface $translator)
    {
        $this->fields     = $fields;
        $this->listModel  = $listModel;
        $this->translator = $translator;
    }

    /**
     * @param $object
     */
    public function addChoicesToEvent(LeadListFiltersChoicesEvent $event, $object)
    {
        $choices = $this->getChoices();
        foreach (self::ALLOWED_TABLES as $table) {
            if (isset($choices[$table])) {
                foreach ($choices[$table] as $key=>$options) {
                    $event->addChoice($table, $key, $options);
                }
            }
        }
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function addChoices($object = null)
    {
        $choices = $this->getchoices();
        foreach (self::ALLOWED_TABLES as $table) {
            if (isset($choices[$table])) {
                foreach ($choices[$table] as $key=>$options) {
                    if (null === $object || ('recommender' == $object && $options['recommender'])) {
                        $this->fieldChoices[$table][$key] =  $options;
                    }
                }
            }
        }

        return $this->fieldChoices;
    }

    /**
     * @return array
     */
    public function getSelectOptions()
    {
        $choices = $this->getChoices();
        $opt     = [];
        foreach (self::ALLOWED_TABLES as $table) {
            if (isset($choices[$table])) {
                foreach ($choices[$table] as $key=>$options) {
                    $opt['mautic.lead.'.$table][$key] = $options['label'];
                }
            }
        }

        return $opt;
    }

    /**
     * @return array
     */
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
                if (isset($properties['type'])) {
                    //   $properties['type'] = RecommenderHelper::typeToTypeTranslator($properties['type']);
                }
                $choices[$table][$key] = [
                    'properties' => $properties,
                    'operators'  => $this->listModel->getOperatorsForFieldType(
                        isset($field['operators']) ? $field['operators'] : $properties['type']
                    ),
                    'recommender'=> isset($field['decorator']['recommender']),
                ];

                switch ($table) {
                    case 'recommenders':
                        $choices[$table][$key]['label'] = $this->translator->trans($field['name']);
                        break;
                    case 'recommender_item':
                        $choices[$table][$key]['label'] = $this->translator->trans('mautic.plugin.recommender.form.item').' '.$this->translator->trans($field['name']);
                        break;
                    case 'recommender_item_property_value':
                        $choices[$table][$key]['label'] =  $this->translator->trans('mautic.plugin.recommender.form.item').' '.$this->translator->trans($field['name']);
                        break;
                    default:
                        $choices[$table][$key]['label'] = $this->translator->trans('mautic.plugin.recommender.form.event').' '.$this->translator->trans($field['name']);
                        break;
                }
            }
        }

        return $choices;
    }
}
