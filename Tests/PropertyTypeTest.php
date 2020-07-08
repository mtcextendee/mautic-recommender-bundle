<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Tests;

use MauticPlugin\MauticRecommenderBundle\Api\Client\Request\AddDetailView;

class PropertyTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPropertyType()
    {
        $clientMock = $this->createMock(Client::class);
        $class      = new AddDetailView($clientMock);
        $this->assertEquals($class->getPropertyType('13749'), 'int');
        $this->assertEquals($class->getPropertyType('Testet string'), 'string');
        $this->assertEquals($class->getPropertyType('2018-09-01 11:11:11'), 'datetime');
        $this->assertEquals($class->getPropertyType('1'), 'int');
        $this->assertEquals($class->getPropertyType('true'), 'boolean');
    }
}
