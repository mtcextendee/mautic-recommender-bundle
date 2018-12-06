<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Symfony\Component\Form\FormBuilderInterface;

class FilterChoiceFormEvent extends CommonEvent
{

    /** @var array  */
    protected $choices;

    /**
     * FilterChoiceFormEvent constructor.
     *
     * @param array $choices
     */
    public function __construct($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @param $label
     * @param $value
     */
    public function addChoice($label, $value)
    {
        $this->choices[$value] = $label;
    }

    /**
     * @param array $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

}
