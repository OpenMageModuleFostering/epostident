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
 * DeutschePost_Postident_Model_Verification
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Verification
{
    /*
     * var Mage_Sales_Model_Quote
     */
    protected $quote = null;

    /**
     * Key for Postident Response-Data in $quote->getAdditionalData()
     * => $quote->getAdditionalData()["postident"]["identData"]
     * 
     * var string 
     */
    protected $postIdentResponseKey = 'identData';

    /**
     * Check if a the user is verified
     * 
     * @return boolean
     */
    public function userIsVerified()
    {
        $userIsVerified = false;

        try {
            //Check if verfification is required
            if (false === $this->isVerificationRequired()) {
                return true;
            }

            //Start to run different verification checks (currently only for min age)
            //Check user for age verification
            if (true === $this->ageCheckIsRequired() && true === $this->verifiyUserByAge()) {
                $userIsVerified = true;
            }

            Mage::dispatchEvent('postident_user_verification_check', array(
                'quote' => $this->getQuote(),
                'userIsVerified' => $userIsVerified
                )
            );
        } catch (Exception $e) {
            Mage::helper("postident/data")->log(
                sprintf("Error in userIsVerified()-check for quote %s with message '%s'", $this->getQuote()->getId(), $e->getMessage()
                )
            );
        }

        return $userIsVerified;
    }

    /**
     * Check if a verification is necessary with the current setup
     * 
     * @return boolean
     */
    public function isVerificationRequired()
    {
        $config = Mage::getModel("postident/config");
        //Verification is not required if module is disabled
        if (false === $config->isEnabled()) {
            return false;
        }

        //Currently we allow only a verification by age
        if (true === $this->ageCheckIsRequired()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if age verification is required and available for the ID-Card
     * 
     * @return boolean
     */
    public function ageCheckIsRequired()
    {
        $config = Mage::getModel("postident/config");
        if ($this->getMinAge() > 0 && true === $config->getSelectedIdCardInstance()->allowAgeCheck()) {
            return true;
        }
        return false;
    }

    /**
     * Check user/quote session for min age
     * 
     * @return boolean
     */
    public function verifiyUserByAge()
    {
        $idCard = Mage::getModel("postident/config")->getSelectedIdCardInstance();
        $verificationData = $this->getPostidentVerificationData();
        if (false === is_array($verificationData) || false === array_key_exists($this->postIdentResponseKey, $verificationData)) {
            //User has no valid verification result yet
            return false;
        } else {
            //User has a valid verification result - run age check
            $ageCheck = $idCard->checkAge($verificationData[$this->postIdentResponseKey]);
            Mage::helper("postident/data")->log(
                sprintf("Age check for quote %s passed with result '%s'", $this->getQuote()->getId(), var_export($ageCheck, true))
            );
            return $ageCheck;
        }
    }
    
    /**
     * returns the min age based on verification type
     * 
     * @return int
     */
    public function getMinAge()
    {
        $minAge = 0;
        
        switch (Mage::getModel("postident/config")->getVerificationType()) {
            case DeutschePost_Postident_Model_System_Config_Source_Verificationtype::GLOBAL_VALUE:
                $minAge = Mage::getModel("postident/config")->getAgeVerification();
                break;
            case DeutschePost_Postident_Model_System_Config_Source_Verificationtype::PRODUCT_SPECIFIC:
                $minAge = Mage::helper('postident/data')->checkCart();
                break;
            case DeutschePost_Postident_Model_System_Config_Source_Verificationtype::BOTH_VALUES:
                $minAge = max(
                    Mage::getModel("postident/config")->getAgeVerification(),
                    Mage::helper('postident/data')->checkCart()
                );
                break;
        }
        return $minAge;
    }

    /**
     * gets postident data from either quote or customer and returns it
     * 
     * @return string 
     */
    public function getPostidentVerificationData()
    {
        if (true === is_array($this->getPostidentVerificationDataFromQuote())) 
        {
            return $this->getPostidentVerificationDataFromQuote();
        }
        
        if (false === Mage::getModel('postident/config')->verifyForEveryCheckout()
            && true  === is_array($this->getPostidentVerificationDataFromCustomer()))
        {
            return $this->getPostidentVerificationDataFromCustomer();
        }
        
        return null;
    }

    /**
     * gets the postident data from customer and returns it
     * 
     * @return null
     */
    public function getPostidentVerificationDataFromCustomer()
    {
        $customer = $this->getCustomer();
        if (false === is_null($customer)) {
            return unserialize($customer->getPostidentVerificationData());
        }
        return null;
    }

    /**
     * gets the postident data from quote and returns it
     * 
     * @return null
     */
    public function getPostidentVerificationDataFromQuote()
    {
        $quote = $this->getQuote();
        if (false === is_null($quote)) {
            Mage::log('data  =' . unserialize($quote->getPostidentVerificationData()));
            return unserialize($quote->getPostidentVerificationData());
        }
        return null;
    }

    /**
     * saves the permited postident data on the quote 
     * 
     * @param array $postidentData
     * @return null
     */
    public function setPostidentVerificationDataToQuote(array $postidentData)
    {
        $quote = $this->getQuote();
        if (false === is_null($quote)) {
            Mage::dispatchEvent('postident_data_to_quote_save_before', array(
                'quote' => $quote,
                'postidentData' => $postidentData
                )
            );

            $quote->setPostidentVerificationData(serialize(array($this->postIdentResponseKey => $postidentData)));
            $quote->save();

            Mage::dispatchEvent('postident_data_to_quote_save_after', array(
                'quote' => $quote,
                'postidentData' => $postidentData
                )
            );
            Mage::helper('postident')->log(
                sprintf('Quote (%s): Saved identdata %s', $quote->getId(), Zend_Json::encode($postidentData))
            );
            return null;
        } else {
            Mage::throwException(
                sprintf(
                    "PostidentVerificationData '%s'could not be saved because quote is empty", Zend_Json::encode($postidentData)
                )
            );
        }
    }

    /**
     * gets the quote from session
     * 
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (true === is_null($this->quote)) {
            $this->quote = Mage::getSingleton('checkout/cart')->getQuote();
        }
        return $this->quote;
    }

    /**
     * sets quote 
     * 
     * @param Mage_Sales_Model_Quote $quote
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * gets the customer from session
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
