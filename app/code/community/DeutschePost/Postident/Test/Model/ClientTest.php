<?php
/**
 * @category   Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Test_Model_ClientTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_ClientTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * @var DeutschePost_Postident_Model_Config
     */
    protected $config;
    
    /**
     * @var DeutschePost_Postident_Model_Client
     */
    protected $client;
    
    /**
     * @var DeutschePost_Postident_Model_Client_Http
     */
    protected $mockHttpClient;
    
    public function setUp()
    {
        $this->store = Mage::app()->getStore(0)->load(0);
        $this->config = Mage::getModel('postident/config');
        $this->client = Mage::getModel('postident/client');
        
        /* mock http client */
        $this->mockHttpClient = $this->getMock(
            'DeutschePost_Postident_Model_Client_Http'
        );
        
        parent::setUp();
    }
    
    public function testGetHttpClient()
    {
        $this->assertEquals(
            "DeutschePost_Postident_Model_Client_Http",
            get_class($this->client->getHttpClient())
        );
    }
    
    public function testSendCheckConnectRequest()
    {
        $clientId = "9cd4cf22-e6cb-4496-b9ca-d9c0de606526";
        $domainUri = "ahe-ce-1510.magento.nrdev.de";
        $gatewayUrl = "https://ident.epost-gka.de/oauth2/clientverification";
        
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->any())
            ->method('requestCheckConnect')
            ->with($this->equalTo($gatewayUrl), $this->equalTo(array("clientId" => $clientId, "domainUri" => $domainUri)))
            ->will($this->returnValue($httpClient));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($httpClient));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        
        //Success case
        try {
            $response = array("status" => "200", "message" => "clientId and domainUri matched");
            
            $httpClient = $this->mockHttpClient;
            $httpClient->expects($this->any())
                ->method('parseCheckConnectJsonResponse')
                ->with($this->equalTo('{"status":200,"message":"clientId and domainUri matched"}'))
                ->will($this->returnValue($response));
            $this->replaceByMock('model', 'postident/client_http', $httpClient);
        } catch (DeutschePost_Postident_Model_Client_Exception $e) {
            $this->fail('DeutschePost_Postident_Model_Client_Exception exception in success case');
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $e) {
            $this->fail('DeutschePost_Postident_Model_Client_Response_Exception exception in success case');
        }
        
        //Error case
        $this->setUp();
        $response = array("status" => "403", "message" => "clientservice activation state is false");
        
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->any())
            ->method('requestCheckConnect')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception()));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        $this->setExpectedException('DeutschePost_Postident_Model_Client_Exception');
        $this->client->sendCheckConnectRequest($clientId, $domainUri, $gatewayUrl);
    }
    
    
    public function testSendAccessTicketRequest()
    {
        $this->store->resetConfig();
        $this->store->setConfig('web/secure/base_url', 'https://example.com/'); 
        $this->store->setConfig('web/unsecure/base_url', 'https://example.com/');
        $this->store->setConfig('postident/master_data/client_id', '123456');
        $this->store->setConfig('postident/master_data/client_secret', 'pass');
        
        $reponseObject = new Varien_Object();
        $reponseObject->setBody('foo');
        
        $gatewayUrl = 'https://ident.epost.de/oauth2/token';

        $accessTicketRequestData = array(
            'code'          => '4711',
            'client_id'     => '123456',
            'client_secret' => 'pass',
            'redirect_uri'  => 'https://example.com/index.php/postident/cart/back/',
            'grant_type'    => 'authorization_code'
        );
        
        
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->any())
            ->method('requestAccessTicket')
            ->with($this->equalTo($gatewayUrl), $this->equalTo($accessTicketRequestData))
            ->will($this->returnValue($httpClient));

        $httpClient->expects($this->any())
            ->method('getLastResponse')
            ->will($this->returnValue($reponseObject));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        $this->assertEquals('foo', Mage::getModel('postident/client')->sendAccessTicketRequest('4711'));
        $this->assertNotEquals('bar', Mage::getModel('postident/client')->sendAccessTicketRequest('4711'));
        
        
         //Error case
        $this->setUp();
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->once())
            ->method('requestAccessTicket')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception()));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        $this->setExpectedException('DeutschePost_Postident_Model_Client_Exception');
        $this->client->sendAccessTicketRequest('foo');
        
    }
    
    public function testSendIdentDataRequest()
    {
        $reponseObject = new Varien_Object();
        $reponseObject->setBody('foo');
        
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->once())
            ->method('requestIdentData')
            ->will($this->returnValue($httpClient));
        
        $httpClient->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue($reponseObject));
        
        $httpClient->expects($this->once())
            ->method('validateIdentDataXmlResponse')
            ->will($this->returnValue('foo'));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        
        $bodyResult = Mage::getModel('postident/client')->sendIdentDataRequest('4711');
        $this->assertEquals('foo', $bodyResult);
        $this->assertNotEquals('bar', $bodyResult);
        
         //Error case
        $this->setUp();
        $httpClient = $this->mockHttpClient;
        $httpClient->expects($this->once())
            ->method('requestIdentData')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception()));
        $this->replaceByMock('model', 'postident/client_http', $httpClient);
        $this->setExpectedException('DeutschePost_Postident_Model_Client_Exception');
        $this->client->sendIdentDataRequest('foo');
    }
    
}