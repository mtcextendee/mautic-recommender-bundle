<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Tests\Recommendere;

use Mautic\CoreBundle\Test\AbstractMauticTestCase;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderToken;

class RecommendereTest extends AbstractMauticTestCase
{
    /**
     * @var RecommenderToken
     */
    protected $recommenderToken;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var ApiCommands
     */
    private $apiCommand;

    /**
     * @var Lead
     */
    private $leadInTest;

    public function setUp()
    {
        parent::setUp();
        $this->leadModel        = $this->container->get('mautic.lead.model.lead');
        $this->apiCommand       = $this->container->get('mautic.recommender.service.api.commands');
        $this->recommenderToken = $this->container->get('mautic.recommender.service.token');
    }

    public function testProcess()
    {
        $lead = $this->createLead();
        if (!$lead->getId()) {
            $this->leadModel->saveEntity($lead);
        }
        $this->leadInTest = $lead;
        $this->assertNotNull($lead);
        $this->assertNotNull($lead->getId());

        $this->apiCommand->importItems($this->getItems()[0]);
        $this->assertForSingleApiCall();

        $this->apiCommand->importItems($this->getItems());
        $this->assertForMultipleApiCall();

        $this->apiCommand->callCommand(
            'AddDetailView',
            $this->getItemsToEvent(true, true)
        );
        $this->assertForSingleApiCall();

        $this->apiCommand->callCommand(
            'AddDetailView',
            $this->getItemsToEvent()
        );
        $this->assertForMultipleApiCall();

        $this->apiCommand->callCommand(
            'AddCartAddition',
            $this->getItemsToEvent(['id', 'amount', 'price'], true)
        );

        $this->assertForSingleApiCall();

        $this->apiCommand->callCommand(
            'AddCartAddition',
            $this->getItemsToEvent(['id', 'amount', 'price'])
        );

        $this->assertForMultipleApiCall();

        // token userId = 1 itemId = 1, limit = 9
        $this->recommenderToken->setToken(['id' => 1, 'userId' => $this->leadInTest->getId(), 'limit' => 9]);

        // check for any items
        $this->apiCommand->callCommand(
            'RecommendItemsToUser',
            $this->recommenderToken->getOptions(true)
        );
        $this->assertTrue(!empty($this->apiCommand->getCommandOutput()['recomms']));

        return;
        $this->apiCommand->callCommand(
            'AddPurchase',
            $this->getItemsToEvent(['id', 'amount', 'price', 'profit'], true)
        );

        $this->assertForSingleApiCall();

        $this->apiCommand->callCommand(
            'AddPurchase',
            $this->getItemsToEvent(['id', 'amount', 'price', 'profit'])
        );
        $this->assertForMultipleApiCall();
    }

    private function assertForSingleApiCall()
    {
        $this->assertEquals($this->apiCommand->getCommandOutput(), 'ok');
    }

    private function assertForMultipleApiCall()
    {
        $this->assertCount(2, $this->apiCommand->getCommandOutput());
        $this->assertArraySubset([0 => ['code' => 200]], $this->apiCommand->getCommandOutput());
    }

    private function createLead()
    {
        $leadEmail = 'kuzmany@gmail.com';
        $firstname = 'Testname';
        $lastname  = 'Testlastname';

        $leadFields              = [];
        $leadFields['email']     = $leadEmail;
        $leadFields['firstname'] = $firstname;
        $leadFields['lastname']  = $lastname;

        return $this->leadModel->checkForDuplicateContact($leadFields);
    }

    private function getLeadData()
    {
        $leadEmail = 'rafoxesi4@loketa.com';
        $firstname = 'Testname';
        $lastname  = 'Testlastname';

        $leadFields              = [];
        $leadFields['email']     = $leadEmail;
        $leadFields['firstname'] = $firstname;
        $leadFields['lastname']  = $lastname;

        return $leadFields;
    }

    private function getItems()
    {
        $items              = [];
        $items[0]['id']     = 1;
        $items[0]['name']   = 'Test product';
        $items[0]['url']    = 'http://recommender.com';
        $items[0]['price']  = '99';
        $items[0]['amount'] = '2';
        $items[0]['profit'] = '19';

        $items[1]['id']     = 2;
        $items[1]['name']   = 'Test product 2';
        $items[1]['price']  = '10';
        $items[1]['amount'] = '2';
        $items[1]['profit'] = '3';

        return $items;
    }

    /**
     * Get Items for tests - single item, multiple item, fill with userId in default.
     *
     * @param bool $keysAttr If true We use just itemId and userId
     * @param bool $first
     * @param bool $userId
     *
     * @return array
     */
    private function getItemsToEvent($keysAttr = true, $first = false, $userId = true)
    {
        $returnItems = [];
        if (true === $keysAttr) {
            $keys = ['id'];
        } else {
            $keys = $keysAttr;
        }

        if (true === $userId) {
            $userId = $this->leadInTest->getId();
        }

        foreach ($this->getItems() as $keyFromItems => $item) {
            foreach ($keys as $key) {
                $keyForArray = $key;
                if ('id' === $key) {
                    $keyForArray = 'itemId';
                }
                if (true == $first) {
                    return [$keyForArray => $item[$key], 'userId' => $userId];
                }
                $returnItems[$keyFromItems][$keyForArray] = $item[$key];
            }
            $returnItems[$keyFromItems]['userId'] = $this->leadInTest->getId();
        }

        return $returnItems;
    }
}
