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
 * DeutschePost_Postident_Test_Model_ObserverTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    
   public function setUp()
   {
       $this->app()->getFrontController()->getRequest()->setBaseUrl('http://example.org');
       parent::setUp();
   }
    
    /**
    * Set order to event object
    *
    * @param Mage_Sales_Model_Order $order
    * @return Varien_Event_Observer
    */
    protected function prepareObserver(Mage_Sales_Model_Order $order)
    {
        $observer = new Varien_Event_Observer;
        $event = new Varien_Event();
        $event->setData('order', $order);
        $observer->setData('event', $event);
        return $observer;

    }
    
    /**
     * test save identData to new customer
     *
     * @test
     * @loadFixture quotes
     */
    public function testSaveIdentDataForNewCustomer()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $customer = Mage::getModel('customer/customer')->load(1);
        $order->setCustomer($customer);
        $observer = $this->prepareObserver($order);
        $quote = Mage::getModel('sales/quote')->load(1);
        $order->setQuote($quote);
        
        $configMock = $this->getModelMock('postident/config', array(
            'isEnabled'
            )
         );
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'saveIdentDataToCustomer'
            )
         );
        $helperMock->expects($this->once())
            ->method('saveIdentDataToCustomer')
            ->with($customer,$quote)
            ->will($this->returnValue($customer));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $sessionMock = $this->getModelMock('customer/session', array(
            'setCustomer', 'renewSession', 'init', 'start'
            )
         );
        $sessionMock->expects($this->once())
            ->method('setCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('model', 'customer/session', $sessionMock);
        Mage::getModel('postident/observer')->saveIdentDataForNewCustomer($observer);
        
    }
    
    /**
     * test save identData to new customer
     *
     * @test
     * @loadFixture quotes
     */
    public function TestSaveIdentDataForNewCustomerWithNoDataOnQuote()
    {
        $order = Mage::getModel('sales/order')->load(2);
        $customer = Mage::getModel('customer/customer')->load(1);
        $order->setCustomer($customer);
        $observer = $this->prepareObserver($order);
        $quote = Mage::getModel('sales/quote')->load(2);
        $order->setQuote($quote);
        $configMock = $this->getModelMock('postident/config', array(
            'isEnabled'
            )
         );
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'saveIdentDataToCustomer'
            )
         );
        
        $helperMock->expects($this->never())
            ->method('saveIdentDataToCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $sessionMock = $this->getModelMock('customer/session', array(
            'setCustomer', 'init', 'renewSession', 'start'
            )
         );
        $sessionMock->expects($this->never())
            ->method('setCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('model', 'customer/session', $sessionMock);
        Mage::getModel('postident/observer')->saveIdentDataForNewCustomer($observer);
        
    }
    
    /**
     * test save identData to new customer
     *
     * @test
     * @loadFixture quotes
     */
    public function testSaveIdentDataWithCheckoutAsGuest()
    {
        $order = Mage::getModel('sales/order')->load(3);
        $customer = Mage::getModel('customer/customer')->load(1);
        $order->setCustomer($customer);
        $observer = $this->prepareObserver($order);
        $quote = Mage::getModel('sales/quote')->load(3);
        $order->setQuote($quote);
        $configMock = $this->getModelMock('postident/config', array(
            'isEnabled'
            )
         );
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'saveIdentDataToCustomer'
            )
         );
        
        $helperMock->expects($this->never())
            ->method('saveIdentDataToCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $sessionMock = $this->getModelMock('customer/session', array(
            'setCustomer', 'init', 'renewSession', 'start'
            )
         );
        $sessionMock->expects($this->never())
            ->method('setCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('model', 'customer/session', $sessionMock);
        Mage::getModel('postident/observer')->saveIdentDataForNewCustomer($observer);
    }
}