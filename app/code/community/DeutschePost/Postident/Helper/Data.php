
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
 * DeutschePost_Postident_Helper_Data
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */
class DeutschePost_Postident_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * log to a separate log file
     *
     * @param string $message
     * @param int    $level
     * @param bool   $force
     * @return DeutschePost_Postident_Helper_Data
     */
    public function log($message, $level = null, $force = false)
    {
        if ($force || Mage::getModel('postident/config')->isLoggingEnabled()) {
            Mage::log($message, $level, 'postident.log', $force);
        }
        return $this;
    }

    /**
     * log Webservice-Request
     *
     * @param string request-Call Title $callTitle
     * @param string webservice-url $webserviceUrl
     * @param array request-Values $request
     * 
     * @return DeutschePost_Postident_Helper_Data
     */
    public function logWebserviceRequest($callTitle, $webserviceUrl, $request)
    {
        $message = sprintf(
            "\n=====================\nRequest Call: %s\nWebservice-Gateway: %s\nRequest: %s\n=====================", $callTitle, $webserviceUrl, Zend_Json::encode($request)
        );
        return $this->log($message);
    }

    /**
     * log Webservice-Response
     *
     * @param string request-Call Title $callTitle
     * @param string webservice-url $webserviceUrl
     * @param array response-Values $response
     * 
     * @return DeutschePost_Postident_Helper_Data
     */
    public function logWebserviceResponse($callTitle, $response)
    {
        $message = sprintf(
            "\n=====================\nResponse: %s\nResponse: %s\n=====================", $callTitle, Zend_Json::encode($response)
        );
        return $this->log($message);
    }

    /**
     * returns the base url for the current store
     * 
     * @return string
     */
    public function getDomainUri()
    {
        $params = Mage::app()->getRequest()->getParams();
        $currentStoreId = 0;
        if (array_key_exists('store', $params) && false != $this->getStoreIdByCode($params['store'])) {
            $currentStoreId = $this->getStoreIdByCode($params['store']);
        }

        $baseUrl = Mage::app()->getStore($currentStoreId)->getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_WEB, true
        );
        $baseUrl = rtrim($baseUrl, "/");
        return str_replace(
            array("https://", "http://"), "", $baseUrl
        );

        return $baseUrl;
    }

    /**
     * get the name of the current store
     * 
     * @return string
     */
    public function getStoreName()
    {
        return Mage::app()->getStore()->getName();
    }

    /**
     * returns the Id for the store via its store code
     * 
     * @param type string $storeCode
     * @return string or Boolean
     */
    protected function getStoreIdByCode($storeCode)
    {
        $stores = array_keys(Mage::app()->getStores());
        foreach ($stores as $id) {
            $store = Mage::app()->getStore($id);
            if ($store->getCode() == $storeCode) {
                return $store->getId();
            }
        }
        return false;
    }

    /**
     * Get current page code
     * 
     * Returns f.e. "checkout-cart-index"
     * 
     * @return string | boolean
     */
    public function getPageCode()
    {
        $action = Mage::app()->getFrontController()->getAction();
        $page_code = '';
        if ($action) {
            $page_code = $action->getFullActionName('-');
        }
        return $page_code;
    }

    /**
     * Get current quote
     * 
     * @return Mage_Sales_Model_Quote 
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::helper('customer')->getCustomer();
    }

    /**
     * saves the identdata from the quote on the customer
     * 
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Sales_Quote $quote
     * 
     * @return Mage_Customer_Model_Customer
     */

    public function saveIdentDataToCustomer($customer = null, $quote = null)
    {
        if (is_null($quote) || (false === $quote instanceof Mage_Sales_Model_Quote)) {
            $quote = $this->getQuote();
        }
        
        if (is_null($customer)) {
            $customer = $this->getCustomer();
        }
        
        $oldCustomerVerficationData = $customer->getPostidentVerificationData();
        $customer->setPostidentVerificationData($quote->getPostidentVerificationData());
        $customer->save();
        $this->log(
            $this->__("Changed customer <%s|%s> postidentData from '%s' to '%s'",
            $customer->getId(),
            $customer->getEmail(),
            $oldCustomerVerficationData,
            $customer->getPostidentVerificationData())
        );
        
        return $customer;
    }

    /**
     * check cart items and return highest min age
     * 
     * @return int
     */
    public function checkCart()
    {
        $quoteItems = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $productMinAge = array(0);
        foreach ($quoteItems as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
            $productMinAge[] = $product->getEpostidentMinage();
        }
        return max($productMinAge);
    }
}
