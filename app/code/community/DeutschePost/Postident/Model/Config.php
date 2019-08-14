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
 * DeutschePost_Postident_Model_Config
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Config
{
    /* ID-Card for Testmode */
    const ID_CARD_TEST = 1304; 
    
    /**
     * Is module enabled?
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (1 == Mage::getStoreConfig('postident/general/active'));
    }

    /**
     * if logging is enabled
     *
     * @return boolean
     */
    public function isLoggingEnabled()
    {
        return (1 == Mage::getStoreConfig('postident/general/logging_enabled'));
    }

    /**
     * get client-id from store config
     * 
     * @return string
     */
    public function getClientId()
    {
        return Mage::getStoreConfig('postident/master_data/client_id');
    }

    /**
     * get client secret from store config
     * 
     * @return string
     */
    public function getClientSecret()
    {
        return Mage::getStoreConfig('postident/master_data/client_secret');
    }

    /**
     * Get all ID-Cards
     *
     * @return array
     */
    public function getAllIdcards()
    {
        return Mage::getStoreConfig('postident/idcards');
    }

    /**
     * get the selected ID-Card
     * 
     * @return string
     */
    public function getSelectedIdCard()
    {
        if ($this->isTestMode() === true) {
            return DeutschePost_Postident_Model_Config::ID_CARD_TEST;
        }
        return Mage::getStoreConfig('postident/idcard/number');
    }
    
    /**
     * get the check connect url that is set in config.xml
     * 
     * @return string
     */
    public function getCheckConnectUrl()
    {
        return Mage::getStoreConfig('postident/gateway_urls/check_connect_url');
    }
    
    /**
     * check if testmode is enabled
     * 
     * @return int
     */
    public function isTestMode()
    {
        return (1 == Mage::getStoreConfig('postident/idcard/testmode'));
    }
    
    /**
     * get the Age thats set in modul config
     * 
     * @return int
     */
    public function getAgeVerification()
    {
        return (int) Mage::getStoreConfig('postident/verification_criteria/age_verification');
    }
    
    /**
     * get the E_POSTIDENT url that is set in config.xml
     * 
     * @return string
     */
    public function getPostidentUrl()
    {
        return Mage::getStoreConfig('postident/gateway_urls/postident_url');
    }
    
    /**
     * Get an instance of an ID-Card
     * 
     * @param int $idCardNumber
     * 
     * @return instanceOf DeutschePost_Postident_Model_IdCard_Abstract
     */
    public function getIdCardInstance($idCardNumber)
    {
        return Mage::getModel("postident/idCard_idCard".$idCardNumber);
    }
    
    /**
     * Get the instance of the selected ID-Card
     * 
     * @return instanceOf DeutschePost_Postident_Model_IdCard_Abstract
     */
    public function getSelectedIdCardInstance()
    {
        return $this->getIdCardInstance($this->getSelectedIdCard());
    }
    
    /**
     * get the access ticket url from config
     * 
     * @return string
     */
    public function getAccessTicketUrl()
    {
        return Mage::getStoreConfig('postident/gateway_urls/access_ticket_url');
    }
    
    /**
     * get the identdata url from config
     * 
     * @return string
     */
    public function getIdentDataUrl()
    {
        return Mage::getStoreConfig('postident/gateway_urls/identdata_url');
    }

    /**
     * get checkout_verification value from config
     * 
     * @return bool
     */
    public function verifyForEveryCheckout()
    {
        return (1 == Mage::getStoreConfig('postident/verification_criteria/checkout_verification'));
    }
    
    /**
     * get verification_type value from config
     * 
     * @return string
     */
    public function getVerificationType()
    {
        return Mage::getStoreConfig('postident/verification_criteria/verification_type');
    }
    
    /**
     * get address data value from config
     * 
     * @return bool
     */
    public function getAddressDataUsage()
    {
        return (1 == Mage::getStoreConfig('postident/checkout/use_address_data'));
    }

}
