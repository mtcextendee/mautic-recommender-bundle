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

class SentEvent extends CommonEvent
{
    /**
     * @var string
     */
    private $apiRequest;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool|array
     */
    private $return;

    /**
     * SentEvent constructor.
     *
     * @param      $apiRequest
     * @param      $options
     * @param bool $return
     */
    public function __construct($apiRequest, $options, $return = false)
    {

        $this->apiRequest = $apiRequest;
        $this->options    = $options;
        $this->return     = $return;
    }

    /**
     * @return string
     */
    public function getApiRequest()
    {
        return $this->apiRequest;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param boolean $return
     */
    public function setReturn($return)
    {
        $this->return = $return;
    }

    /**
     * @return array|bool
     */
    public function getReturn()
    {
        return $this->return;
    }
}
