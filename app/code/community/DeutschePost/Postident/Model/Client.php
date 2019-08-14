<?php
/**
 * @category   DeutschePost Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Model_Client
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Client
{

    
    /**
     * Get HTTP-Client
     * 
     * @return DeutschePost_Postident_Model_Client_Http
     */
    public function getHttpClient()
    {
        return Mage::getModel('postident/client_http');
    }
    
    /**
     * Get the data helper
     * 
     * @return DeutschePost_Postident_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper("postident");
    }

    /**
     * send check request with the needed data
     *
     * @param string $clientId
     * @param string $domain
     * 
     * @return Zend_Http_Response
     * @throws DeutschePost_Postident_Model_Client_Exception
     */
    public function sendCheckConnectRequest($clientId, $domain, $checkConnectUrl = null)
    {
        $helper = $this->getHelper();
        $config = Mage::getModel('postident/config');

        $checkConnectData = array(
            'clientId' => $clientId,
            'domainUri' => $domain
        );

        if (null == $checkConnectUrl) {
            $checkConnectUrl = $config->getCheckConnectUrl();
        }

        try {
            $httpClient = $this->getHttpClient();
            $httpClient->requestCheckConnect(
                $checkConnectUrl, $checkConnectData
            );
            return $httpClient->parseCheckConnectJsonResponse($httpClient->getLastResponse()->getBody());
        } catch (Zend_Http_Client_Adapter_Exception $e) {
            //throw DeutschePost_Postident_Model_Client_Exception
            DeutschePost_Postident_Model_Client_Exception::connectError($e->getMessage());
        }
    }

    /**
     * request the access token with the unaltered response code
     * 
     * @param string $accessToken
     * @return void
     * @throws DeutschePost_Postident_Model_Client_Exception
     */
    public function sendAccessTicketRequest($accessToken)
    {
        $helper     = $this->getHelper();
        $gatewayUrl = Mage::getModel('postident/config')->getAccessTicketUrl();

        $accessTicketRequestData = array(
            'code'          => $accessToken,
            'client_id'     => Mage::getModel('postident/config')->getClientId(),
            'client_secret' => Mage::getModel('postident/config')->getClientSecret(),
            'redirect_uri'  => Mage::getUrl('postident/cart/back/', array('_secure' => true, '_nosid' => true)),
            'grant_type'    => 'authorization_code'
        );

        try {
            $httpClient = $this->getHttpClient();
            $httpClient->requestAccessTicket(
                $gatewayUrl,
                $accessTicketRequestData
            );
            $accessToken = $httpClient->getLastResponse()->getBody();
            
            //Log
            Mage::helper("postident/data")->logWebserviceResponse(
                "Access Ticket", 
                $accessToken
            );
            
            return $accessToken;
        } catch (Zend_Http_Client_Adapter_Exception $e) {
            //throw DeutschePost_Postident_Model_Client_Exception
            DeutschePost_Postident_Model_Client_Exception::accessTicketError($e->getMessage());
        }
    }
    
    /**
     * request the ident data with the provided access token
     * 
     * @param array $accessToken
     * @return string
     * @throws DeutschePost_Postident_Model_Client_Exception
     */
    public function sendIdentDataRequest($accessToken)
    {
        $helper     = $this->getHelper();
        $gatewayUrl = Mage::getModel('postident/config')->getIdentDataUrl();
        $accessToken = array(
            'Authorization' => $accessToken
        );

        try {
            $httpClient = $this->getHttpClient();
            $httpClient->requestIdentData(
                $gatewayUrl,
                $accessToken
            );
           return $httpClient->validateIdentDataXmlResponse($httpClient->getLastResponse()->getBody()); 
        } catch (Zend_Http_Client_Adapter_Exception $e) {
            //throw DeutschePost_Postident_Model_Client_Exception
            DeutschePost_Postident_Model_Client_Exception::identDataError($e->getMessage());
        }
    }

}
