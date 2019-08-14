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
 * DeutschePost_Postident_Test_Helper_DataTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected $config;

    public function setUp()
    {
        
        $this->helper  =  Mage::helper('postident');
        $this->config = Mage::getModel('postident/config');
        parent::setUp();
    }
    
    public function testLog()
    {
        $this->assertTrue(true);
    }
    
    public function testGetDomainUri()
    {
        $store  = Mage::app()->getStore(1)->load(1);
        $params = array('store' => 'default');
        Mage::app()->getRequest()->setParams($params);
        $helperMock = $this->getHelperMock('postident/data');
        $helperMock->expects($this->any())
            ->method('getStoreIdByCode')
            ->with($this->equalTo('default'))
            ->will($this->returnValue(1));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        $store->resetConfig();
        $store->setConfig('web/secure/base_url','https://www.test.de/' );
        $this->assertEquals('www.test.de', $this->helper->getDomainUri());
    }
    
     /**
     * test set save identData to customer
     *
     * @test
     * @loadFixture quotes
     * @loadExpectation quotes
     */
    public function testSaveIdentDataToCustomer()
    {
        
        // no verification data on customer
        $customer = Mage::getModel('customer/customer')->load(1);
        $this->assertEquals(null, $customer->getPostidentVerificationData());
        
        // verification data on quote
        $quote = Mage::getModel('sales/quote')->load(2);
        $helperMock = $this->getHelperMock('postident/data', array(
            'getQuote',
            'getCustomer'
            )
        );
        $helperMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        
        $helperMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
         /**
          * No matter if parameters are null or not, the result 
          * allways should be the same !
          * 
          * 
         */
        
        //test if customer and quote is given as parameter
        Mage::helper('postident/data')->saveIdentDataToCustomer($customer, $quote);
        $this->assertEquals(
            $quote->getPostidentVerificationData(),
            Mage::getModel('customer/customer')->load(1)->getPostidentVerificationData()
        );
        //test if only customer is given as parameter
        Mage::helper('postident/data')->saveIdentDataToCustomer($customer, null);
        $this->assertEquals(
            $quote->getPostidentVerificationData(),
            Mage::getModel('customer/customer')->load(1)->getPostidentVerificationData()
        );
        
        //test if only quote is given as parameter
        Mage::helper('postident/data')->saveIdentDataToCustomer(null, $quote);
        $this->assertEquals(
            $quote->getPostidentVerificationData(),
            Mage::getModel('customer/customer')->load(1)->getPostidentVerificationData()
        );
        
        //test if nothing is given as parameter
        Mage::helper('postident/data')->saveIdentDataToCustomer(null, null);
        $this->assertEquals(
            $quote->getPostidentVerificationData(),
            Mage::getModel('customer/customer')->load(1)->getPostidentVerificationData()
        );
    }
    
    /**
     * test check cart for min age items
     *
     * @test
     * @loadFixture quotes
     */
    public function testCheckCart()
    {
       

        $customerSession = $this->getModelMock('customer/session', array('getQuote', 'start', 'renewSession', 'init'));
        $this->replaceByMock('model', 'customer/session', $customerSession);
        
        $itemsCollection = array();
        $product = new Varien_Object();
        $product->setId(1);
        $item = new Varien_Object();
        $item->setProduct($product);
        $item->setId(1);
        $itemsCollection[] = $item;
        $item = new Varien_Object();
        $product->setId(2);
        $item->setProduct($product);
        $item->setId(2);
        $itemsCollection[] = $item;
        $item = new Varien_Object();
        $product->setId(3);
        $item->setProduct($product);
        $item->setId(3);
        $itemsCollection[] = $item;
        $quoteMock = $this->getModelMock('sales/quote', array('getAllItems'));
        $quoteMock->expects($this->any())
                ->method('getAllItems')
                ->will($this->returnValue($itemsCollection));
        $this->replaceByMock('model', 'sales/quote', $quoteMock);
        $quote = Mage::getModel('sales/quote')->load(2);
        $checkoutSession = $this->getModelMock('checkout/session', array('getQuote', 'start', 'renewSession', 'init'));
        $checkoutSession->expects($this->any())
             ->method('getQuote')
             ->will($this->returnValue($quote));
        $this->replaceByMock('model', 'checkout/session', $checkoutSession);
        
        $this->assertEquals(21, Mage::helper('postident/data')->checkCart());
    }
}