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
 * DeutschePost_Postident_Helper_Client
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */
class DeutschePost_Postident_Helper_Client extends Mage_Core_Helper_Abstract
{
    /**
     * Validate the redirect response from E-POSTIDENT
     * 
     * Check if "code" and "state" - parameters were correctly set
     * 
     * @param array $response
     * 
     * @return void
     */
    public function validateRedirectResponse($response)
    {
        Mage::helper('postident')->logWebserviceResponse(
                'Postident-Redirect-Response',
                $response);
        
        if (is_null($response) 
            || false === array_key_exists('code', $response) 
            || false === array_key_exists('state', $response)
            || (true === array_key_exists('state', $response) && true  === is_null($response['state'])) 
            || (true === array_key_exists('code', $response) && true  === is_null($response['code']))) 
        {
           throw new DeutschePost_Postident_Helper_Client_Exception(
               "Response is Null or parameter 'code' or 'state' doesn't exist");
        }
        
        if ((int) $response['state'] != (int) Mage::helper('postident')->getQuote()->getId())
        {
            throw new DeutschePost_Postident_Helper_Client_Exception(sprintf(
               "Response-Quote_Id = %s doesn't match Shop-Quote_Id = %s from session.",
                $response['state'],
                Mage::helper('postident')->getQuote()->getId()
            ));
        }
    }
    
    /**
     * build the redirect link
     * 
     * @return string
     */
    public function buildRedirectLink()
    {
        $helper    = Mage::helper('postident/data');
        $redirect  = Mage::getModel('postident/config')->getPostidentUrl();
        $urlParams = array(
            'client_id'     => Mage::getModel('postident/config')->getClientId(),
            'redirect_uri'  => Mage::getUrl('postident/cart/back/', array('_secure' => true, '_nosid' => true)),
            'scope'         => Mage::getModel('postident/config')->getSelectedIdCard(),
            'reason'        => urlencode($helper->__('Your online purchase at %s.', $helper->getStoreName())),
            'response_type' => 'code',
            'state'         => $helper->getQuote()->getId()
        );
        //Log
        $helper->logWebserviceRequest(
            "E-POSTIDENT-Request Link generation",
            $redirect,
            http_build_query($urlParams)
        );
        return $redirect . '?' . http_build_query($urlParams);
    }
    
    /**
     * add verificatrion data to identdata xml
     * transform to array and save it on quote
     * 
     * @param SimpleXMLObject $identDataXml
     * @return void
     */
    public function saveIdentDataToQuote($identDataXml)
    {
        $identDataXml->addChild('verification_date', 
            Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        $identDataArray = json_decode(json_encode($identDataXml), 1);
        Mage::getModel('postident/verification')->setPostidentVerificationDataToQuote($identDataArray);
    }
}