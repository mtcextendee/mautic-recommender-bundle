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


use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use Symfony\Component\Translation\TranslatorInterface;

class Choices
{
    CONST ALLOWED_TABLES = ['recommender_item_property_value', 'recommender_event_log', 'recommender_event_log_property_value'];

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
     *
     * @param Fields              $fields
     * @param ListModel           $listModel
     * @param TranslatorInterface $translator
     */
    public function __construct(Fields $fields, ListModel $listModel, TranslatorInterface $translator)
    {

        $this->fields = $fields;
        $this->listModel = $listModel;
        $this->translator = $translator;
    }

    public function addChoices($object)
    {
        $choices = $this->getChoices();
        foreach (self::ALLOWED_TABLES as $table) {
            foreach ($choices[$table] as $key=>$options) {
                $this->fieldChoices[$table][$key] =  $options;
            }
        }
        return $this->fieldChoices;

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
                if (isset($properties['type'])) {
                 //   $properties['type'] = RecommenderHelper::typeToTypeTranslator($properties['type']);
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
