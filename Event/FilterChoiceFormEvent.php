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
    /** @var array */
    protected $choices;

    /**
     * @param $alias
     * @param $label
     * @param $value
     */
    public function addChoice($alias, $label, $value)
    {
        $this->choices[$alias][$value] = $label;
    }

    /**
     * @param $alias
     * @param $choices
     */
    public function setChoices($alias, $choices)
    {
        $this->choices[$alias] = $choices;
    }

    /**
     * @param $alias
     *
     * @return mixed
     */
    public function getChoices($alias)
    {
        return $this->choices[$alias];
    }
}
