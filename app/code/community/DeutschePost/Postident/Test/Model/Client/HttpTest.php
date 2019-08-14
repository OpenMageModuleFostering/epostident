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
 * DeutschePost_Postident_Test_Model_Client_HttpTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_Client_HttpTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var DeutschePost_Postident_Model_Client_Http
     */
    protected $httpClient;

    /**
     * @var DeutschePost_Postident_Model_Client_Http
     */
    protected $mockHttpClient;

    public function setUp()
    {
        $this->httpClient = Mage::getModel('postident/client_http');

        /* mock http client */
        $this->mockHttpClient = $this->getMock(
            'DeutschePost_Postident_Model_Client_Http'
        );
    }

    public function testRequestCheckConnect()
    {
        $clientId = "9cd4cf22-e6cb-4496-b9ca-d9c0de606526";
        $domainUri = "ahe-ce-1510.magento.nrdev.de";
        $gatewayUrl = "https://ident.epost-gka.de/oauth2/clientverification";

        $mockHttpClient = $this->mockHttpClient;
        $mockHttpClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($mockHttpClient));
        $this->replaceByMock('model', 'postident/client_http', $mockHttpClient);

        $response = $this->httpClient->requestCheckConnect($gatewayUrl, array("clientId" => $clientId, "domainUri" => $domainUri));
        $this->assertTrue("DeutschePost_Postident_Model_Client_Http" == get_class($response));
        $this->assertEquals("https://ident.epost-gka.de:443/oauth2/clientverification", $response->getUri(true));
    }

    public function testParseCheckConnectJsonResponse()
    {
        $response = array("status" => 200, "message" => "clientId and domainUri matched");

        $httpClient = Mage::getModel('postident/client_http');

        try {
            $bodyText = '{"status":200,"message":"clientId and domainUri matched"}';
            $this->assertEquals($response, $this->httpClient->parseCheckConnectJsonResponse($bodyText));
        } catch (Exception $e) {
            $this->fail("Unexpected Exception '{$e->getMessage()}' in success case");
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '{"wrong_key":404}';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals("Client-Response Error: Unable to parse JSON E-POSTIDENT response", $expected->getMessage());
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals('Client-Response Error: Unable to parse JSON E-POSTIDENT response', $expected->getMessage());
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '{"status":404,"message":"clientId mismatched"}';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals("Client-Response Error: The Client-ID doens't exist", $expected->getMessage());
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '{"status":403,"message":"clientservice activation state is false"}';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals("Client-Response Error: The Client-ID exists but was not activated yet", $expected->getMessage());
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '{"status":400,"message":"clientId matched but domainUri mismatched"}';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals("Client-Response Error: The Client-ID exists but the Domain-URI doesn't match to it", $expected->getMessage());
        }

        try {
            $httpClient = Mage::getModel('postident/client_http');
            $bodyText = '{"status":999,"message":"The end is near"}';
            $this->httpClient->parseCheckConnectJsonResponse($bodyText);
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $expected) {
            $this->assertEquals('Client-Response Error: Unknown Check-Connect-Response-Code', $expected->getMessage());
        }
    }

    public function testRequestAccessTicket()
    {
        $accessTokenRequestData = array(
            'clientId'      => "05337686-ff03-4c9c-aff9-6a3823e0faf0",
            'domainUri'     => "ser-dhlpi.magento.nrdev.de ",
            'clientSecret'  => "sebastian80165",
            'code'          => "code12345"
        );
        $gatewayUrl = "https://ident.epost-gka.de/oauth2/token";

        $mockHttpClient = $this->mockHttpClient;
        $mockHttpClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($mockHttpClient));
        $this->replaceByMock('model', 'postident/client_http', $mockHttpClient);

        $response = $this->httpClient->requestAccessTicket($gatewayUrl, $accessTokenRequestData);
        $this->assertTrue("DeutschePost_Postident_Model_Client_Http" == get_class($response));
        $this->assertEquals("https://ident.epost-gka.de:443/oauth2/token", $response->getUri(true));
    }
    
    public function testRequestIdentData()
    {
        $helperMock = $this->getHelperMock('postident/data', array(
            'logWebserviceRequest',
            '__'
        ));
        $helperMock
            ->expects($this->any())
            ->method('logWebserviceRequest')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
       $accessTokenData = array(
            'code'          => "code12345"
        );
        $gatewayUrl = "https://ident.epost-gka.de/oauth2/identdata";

        $mockHttpClient = $this->mockHttpClient;
        $mockHttpClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($mockHttpClient));
        $this->replaceByMock('model', 'postident/client_http', $mockHttpClient);

        $response = $this->httpClient->requestIdentData($gatewayUrl, $accessTokenData);
        $this->assertTrue("DeutschePost_Postident_Model_Client_Http" == get_class($response));
        $this->assertEquals("https://ident.epost-gka.de:443/oauth2/identdata", $response->getUri(true));
    }
    
    public function testValidateIdentDataXmlResponse()
    {
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'logWebserviceResponse',
            '__'
        ));
        $helperMock
            ->expects($this->any())
            ->method('logWebserviceResponse')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $bodyText = '<?xml version="1.0" encoding="UTF-8"?>
                        <identdata>
                            <givenname>Anton</givenname>
                            <familyname>A-netresearchdemo02</familyname>
                            <dateofbirth>1992-11-19 00:00:00.0</dateofbirth>
                            <street>Moltkestraße</street>
                            <housenumber>14</housenumber>
                            <zipcode>53173</zipcode>
                            <city>Bonn</city>
                            <country>DE</country>
                            <epostaddress>anton.a-netresearchdemo02@epost-gka.de</epostaddress>
                         </identdata>';

        $simpleXML = simplexml_load_string($bodyText);
        $this->assertEquals($simpleXML,$this->httpClient->validateIdentDataXmlResponse($bodyText));
        $this->assertNotEquals('foo',$this->httpClient->validateIdentDataXmlResponse($bodyText));
        
        //Error case
        $this->setUp();
        
        $bodyText = '<?xml version="1.0" encoding="UTF-8"?>
                        <identdata>
                            <givenname>Anton</givenname>
                            <familyname>A-netresearchdemo02</familyname>
                            <dateofbirth>1992-11-19 00:00:00.0</dateofbirth>
                            <street>Moltkestraße</street>
                            <housenumber>14';

        $this->setExpectedException('DeutschePost_Postident_Model_Client_Response_Exception');
        $this->httpClient->validateIdentDataXmlResponse($bodyText);
    }
}