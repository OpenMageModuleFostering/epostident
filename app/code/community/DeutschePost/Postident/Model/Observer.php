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
 * DeutschePost_Postident_Model_Observer
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Observer
{

    /**
     * Check if customer is authorized to enter the checkout
     * 
     * If customer is not identified -> redirect to cart with notice
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return void
     */
    public function checkIsVerified(Varien_Event_Observer $observer)
    {
        if (!Mage::getModel('postident/config')->isEnabled()) {
            return;
        }

        /* @var $controller Mage_Checkout_CartController */
        $controller = $observer->getControllerAction();
        if (($controller->getRequest()->getControllerName() == 'onepage' && $controller->getRequest()->getActionName() != 'success') && false === Mage::getModel('postident/verification')->userIsVerified()
        ) {
            //Set quote to hasErrors -> this causes a redirect to cart in all cases
            $controller->getOnepage()->getQuote()->setHasError(true);

            //Add notice message, that the user has to be verified before he is allowed to enter the checkout
            Mage::getSingleton("core/session")->addNotice(
                Mage::helper("postident")->__("An identification by E-POSTIDENT is necessary to enter the checkout.")
            );
        }
    }

    /**
     * Add a layout handle on "checkout_cart_index"-page
     * 
     * IF veritifaction doesn't exist for the customer
     * AND verification is required
     * THEN add the layout handle
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return void
     */
    public function addLayoutHandle(Varien_Event_Observer $observer)
    {
        /* @var $update Mage_Core_Model_Layout_Update */
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $userIsVerified = Mage::getModel("postident/verification")->userIsVerified();

        //New handle for all store parts
        if (true === $this->getConfig()->isEnabled() && false === $userIsVerified) {
            $update->addHandle('postident_verification_required');
        }

        //New handle for shoppinh_cart
        if (true === $this->getConfig()->isEnabled() && "checkout-cart-index" == $this->getHelper()->getPageCode() && false === $userIsVerified) {
            $update->addHandle('postident_checkout_cart_verification_required');
        }
    }

    
    /**
     * Save ident data from quote to customer after placing an order
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function saveIdentDataForNewCustomer(Varien_Event_Observer $observer)
    {
        if (!Mage::getModel('postident/config')->isEnabled()) {
            return;
        }
        
        $quote = $observer->getEvent()->getOrder()->getQuote();
        $customer = $observer->getEvent()->getOrder()->getCustomer();
        $checkoutMethod = $quote->getCheckoutMethod();
        
        if (!is_null($quote)
             && !is_null($quote->getPostidentVerificationData())
             && $checkoutMethod != Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST
            ) {
            $customer = Mage::Helper('postident/data')->saveIdentDataToCustomer($customer, $quote);
            
            //This updates the customer object in the session - ensures that it has the postident data
            Mage::getSingleton('customer/session')->setCustomer($customer);
        }
    }
    
    
    /**
     * add address template to billing step
     *
     * @param  $observer
     * @return void
     */
    public function appendAddressDataToBillingStep($observer)
    {
        if( false === Mage::getModel('postident/config')->isEnabled()
            || false === Mage::getModel('postident/config')->getAddressDataUsage()) {
            return;
        }
        
        if ($observer->getBlock() instanceof Mage_Checkout_Block_Onepage_Billing 
            && false == $observer->getBlock() instanceof Mage_Paypal_Block_Express_Review_Billing
        ) {
            $transport = $observer->getTransport();
            $block     = $observer->getBlock();
            $layout    = $block->getLayout();
            $html      = $transport->getHtml();
            $addAddressTemplateHtml = $layout->createBlock(
                'postident/checkout_onepage_billing', 'postident_onepage_billing')
                ->renderView();
            $html = $html . $addAddressTemplateHtml;
            $transport->setHtml($html);
        }
    }
    
    /**
     * add address template to shipping step
     *
     * @param  $observer
     * @return void
     */
    public function appendAddressDataToShippingStep($observer)
    {
        if( false === Mage::getModel('postident/config')->isEnabled()
            || false === Mage::getModel('postident/config')->getAddressDataUsage()) {
            return;
        }
        
        if ($observer->getBlock() instanceof Mage_Checkout_Block_Onepage_Shipping
            && false == $observer->getBlock() instanceof Mage_Paypal_Block_Express_Review_Shipping
        ) {
            $transport = $observer->getTransport();
            $block     = $observer->getBlock();
            $layout    = $block->getLayout();
            $html      = $transport->getHtml();
            $addAddressTemplateHtml = $layout->createBlock(
                'postident/checkout_onepage_shipping', 'postident_onepage_billing')
                ->renderView();
            $html = $html . $addAddressTemplateHtml;
            $transport->setHtml($html);
        }
    }

    /**
     * Get Postident Helper
     * 
     * @return DeutschePost_Postident_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper("postident");
    }

    /**
     * Get Postident Config Model
     * 
     * @return DeutschePost_Postident_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel("postident/config");
    }
}
