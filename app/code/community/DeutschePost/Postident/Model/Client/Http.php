<?php
/**
 * @category   DeutschePost_Postident
 * @package    DeutschePost_Postident
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Model_Client_Http
 * @author     André Herrn <andre.herrn@netresearch.de> 
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Client_Http extends Zend_Http_Client
{

    /**
     *
     * neccessary override because Mage::getModel passes unwanted params to the client
     *
     * @override
     */
    public function __construct()
    {
        parent::__construct(null, null);
    }

    /**
     *
     * Request Check Connect
     * 
     * @param string $checkConnectUrl
     * @param array $checkConnectData
     *
     * @return DeutschePost_Postident_Model_Client_Http
     */
    public function requestCheckConnect($checkConnectUrl, $checkConnectData)
    {
        $this->setMethod(Zend_Http_Client::GET);

        $this->setUri($checkConnectUrl);
        $this->setParameterGet($checkConnectData);

        //Log
        Mage::helper("postident/data")->logWebserviceRequest(
            "CheckConnect", 
            $checkConnectUrl, 
            $checkConnectData
        );

        parent::request();
        return $this;
    }

    /**
     *
     * Process Check Connect JSON Response
     * 
     * @param string $bodyText
     *
     * @return array
     * @throws DeutschePost_Postident_Model_Client_Response_Exception
     */
    public function parseCheckConnectJsonResponse($bodyText)
    {
        $helper = Mage::helper("postident");
        $response = Zend_Json::decode($bodyText);

        //Log
        Mage::helper("postident/data")->logWebserviceResponse("CheckConnect", $response);

        //Check if JSON-Data exists
        if (false === isset($response["status"]) || false === isset($response["message"])) {
            DeutschePost_Postident_Model_Client_Response_Exception::checkConnectError('Unable to parse JSON E-POSTIDENT response');
        }

         //Case success
        if ('200' == $response["status"]) {
            return $response;
        }

        /*
         * Error cases start here
         * 
         * Check Response Codes
         */
        $errorMessage = "";
        switch ($response["status"]) {
            case '404':
                $errorMessage = "The Client-ID doens't exist";
                break;

            case '403':
                $errorMessage = "The Client-ID exists but was not activated yet";
                break;

            case '400':
                $errorMessage = "The Client-ID exists but the Domain-URI doesn't match to it";
                break;

            default:
                $errorMessage = "Unknown Check-Connect-Response-Code";
                break;
        }
        //throw DeutschePost_Postident_Model_Client_Response_Exception
        DeutschePost_Postident_Model_Client_Response_Exception::checkConnectError($errorMessage);
    }

    /**
     * Request access ticket
     * 
     * @param string $authorization_code
     * @param string $gateway
     * @return DeutschePost_Postident_Model_Client_Http
     */
    public function requestAccessTicket($gatewayUrl, $accessTicketRequestData)
    {
        $this->setMethod(Zend_Http_Client::POST);
        $this->setEncType('application/x-www-form-urlencoded');
        $this->setUri($gatewayUrl);
        $this->setParameterPost($accessTicketRequestData);

        //Log
        Mage::helper("postident/data")->logWebserviceRequest(
            "Access Ticket", 
            $gatewayUrl, 
            $accessTicketRequestData
        );
        parent::request();
        return $this;
    }

    /**
     * Request identity data
     * 
     * @param string $accessToken
     * @param string $gatewayUrl
     * @return DeutschePost_Postident_Model_Client_Http
     */
    public function requestIdentData($gatewayUrl, $accessToken)
    {
        $this->setMethod(Zend_Http_Client::GET);
        $this->setUri($gatewayUrl);
        $this->setHeaders($accessToken);
        
        //Log
        Mage::helper("postident/data")->logWebserviceRequest(
            "IdentData", 
            $gatewayUrl, 
            $accessToken
        );
        parent::request();
        return $this;
    }
    
    /**
     * check if node givenname exists and returns simpleXml object
     * 
     * @param xml $bodyText
     * @return SimpleXMLElement
     * @throws DeutschePost_Postident_Model_Client_Response_Exception
     */
    public function validateIdentDataXmlResponse($bodyText)
    {
       //Log
        Mage::helper("postident/data")->logWebserviceResponse(
            "IdentData", 
            $bodyText
        );
        
        try {
            $simpleXMLResponse = simplexml_load_string($bodyText);
            if (isset($simpleXMLResponse->givenname)) {
                return $simpleXMLResponse;
            } else {
                //throw DeutschePost_Postident_Model_Client_Response_Exception
                DeutschePost_Postident_Model_Client_Response_Exception::throwValidateXMlResponseError('XML Validation failed.');
            }
        } catch (Exception $e) {
            //throw DeutschePost_Postident_Model_Client_Response_Exception
            DeutschePost_Postident_Model_Client_Response_Exception::throwValidateXMlResponseError($e->getMessage());
        }
    }
}
