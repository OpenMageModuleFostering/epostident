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
 * DeutschePost_Postident_Test_Model_VerificationTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_VerificationTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;
    
    /**
     * @var Mage_Postident_Model_Verification
     */
    protected $verification = null;

    public function setUp()
    {
        $sessionMock = $this->getModelMock('customer/session', array(
            'init', 'renewSession', 'start'
            )
         );
        $this->replaceByMock('model', 'customer/session', $sessionMock);
        
        $sessionMock = $this->getModelMock('checkout/session', array(
            'init', 'renewSession', 'start'
            )
         );
        $this->replaceByMock('model', 'checkout/session', $sessionMock);
        $this->store = Mage::app()->getStore(0)->load(0);
        $this->verification = Mage::getModel('postident/verification');
        parent::setup();
    }

    public function testSetQuote()
    {
        $this->setUp();
        $quote = Mage::getModel('sales/quote');
        $quote->setId(1);
        $this->assertEquals(null, $this->verification->getQuote()->getId());
        $this->verification->setQuote($quote);
        $this->assertEquals(1, $this->verification->getQuote()->getId());
    }

    public function testGetQuote()
    {
        $this->setUp();
        $this->assertNotNull($this->verification->getQuote());
        $this->assertTrue($this->verification->getQuote() instanceof Mage_Sales_Model_Quote);
    }
    
    public function testGetCustomer()
    {
        $this->setUp();
        $this->assertNotNull($this->verification->getCustomer());
        $this->assertTrue($this->verification->getCustomer() instanceof Mage_Customer_Model_Customer);
    }

    public function testAgeCheckIsRequired()
    {
        $this->setUp();
        
        //Successful test - Min age is set and a id card with age check was selected
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);
       
        $verificationMock = $this->getModelMock('postident/verification', array(
            'getMinAge'
            )
         );
        $verificationMock->expects($this->any())
            ->method('getMinAge')
            ->will($this->returnValue(18));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertTrue(Mage::getModel('postident/verification')->ageCheckIsRequired());
        
        //Failed test - Min age is not set or 0 and a id card with age check was selected
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);
        $verificationMock = $this->getModelMock('postident/verification', array(
            'getMinAge'
            )
         );
        $verificationMock->expects($this->any())
            ->method('getMinAge')
            ->will($this->returnValue(0));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertFalse(Mage::getModel('postident/verification')->ageCheckIsRequired());
        
        //Failed test - Min age is not set or 0 and a id card with age check was selected
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);
        $this->assertFalse(Mage::getModel('postident/verification')->ageCheckIsRequired());
        
        //Failed test - Min age is set and a id card without age check was selected
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 40);
        $this->store->setConfig("postident/idcard/testmode", 0);
        $verificationMock = $this->getModelMock('postident/verification', array(
            'getMinAge'
            )
         );
        $verificationMock->expects($this->any())
            ->method('getMinAge')
            ->will($this->returnValue(18));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertFalse(Mage::getModel('postident/verification')->ageCheckIsRequired());
        
        //Failed test - Min age is not set and a id card without age check was selected
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 40);
        $this->store->setConfig("postident/idcard/testmode", 0);
        $this->store->setConfig("postident/verification_criteria/age_verification", 0);
        $this->assertFalse(Mage::getModel('postident/verification')->ageCheckIsRequired());
    }

    public function testIsVerificationRequired()
    {
        $this->setUp();
        
        //Test for verification required
        $this->store->resetConfig();
        $this->store->setConfig("postident/general/active", 1);
        $verificationMock = $this->getModelMock('postident/verification', array('ageCheckIsRequired'));
        $verificationMock->expects($this->once())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertTrue(Mage::getModel('postident/verification')->isVerificationRequired());
        
        //Verification not required
        $this->store->resetConfig();
        $this->store->setConfig("postident/general/active", 0);
        $this->assertFalse(Mage::getModel('postident/verification')->isVerificationRequired());
        
        //Verification not required
        $this->store->resetConfig();
        $this->store->setConfig("postident/general/active", 1);
        $verificationMock = $this->getModelMock('postident/verification', array('ageCheckIsRequired'));
        $verificationMock->expects($this->once())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(false));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertFalse(Mage::getModel('postident/verification')->isVerificationRequired());
    }

    public function testVerifiyUserByAgeSuccess()
    {
        $this->setUp();
        $postIdentData =  array( 
            'identData'  => array(
                'givenname'    => 'anton',
                'dateofbirth'  => '1980-05-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        
        
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);
        
        $helperMock = $this->getHelperMock('postident/data', array('log'));
        $helperMock->expects($this->once())
            ->method('log')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);

        $verificationMock = $this->getModelMock('postident/verification', array('getPostidentVerificationData'));
        $verificationMock->expects($this->once())
            ->method('getPostidentVerificationData')
            ->will($this->returnValue($postIdentData));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $idCardMock = $this->getModelMock('postident/idCard_idCard10', array('checkAge'));
        $idCardMock->expects($this->once())
            ->method('checkAge')
            ->with($this->equalTo($postIdentData["identData"]))
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/idCard_idCard10', $idCardMock);
        
        $this->assertTrue(Mage::getModel('postident/verification')->verifiyUserByAge());
    }
    
    public function testVerifiyUserBecauseOfMissingPostidentData()
    {
        $this->setUp();
        
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);
        
        $helperMock = $this->getHelperMock('postident/data', array('log'));
        $helperMock->expects($this->never())
            ->method('log')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);

        $verificationMock = $this->getModelMock('postident/verification', array('getPostidentVerificationDataFromQuote'));
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromQuote')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $idCardMock = $this->getModelMock('postident/idCard_idCard10', array('checkAge'));
        $idCardMock->expects($this->never())
            ->method('checkAge')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/idCard_idCard10', $idCardMock);
        
        $this->assertFalse(Mage::getModel('postident/verification')->verifiyUserByAge());
    }
    
    public function testVerifiyUserBecauseOfFailedAgeCheck()
    {
        $this->setUp();
        
        $this->store->resetConfig();
        $this->store->setConfig("postident/idcard/number", 10);
        $this->store->setConfig("postident/idcard/testmode", 0);

        $helperMock = $this->getHelperMock('postident/data', array('log'));
        $helperMock->expects($this->once())
            ->method('log')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);

        $verificationMock = $this->getModelMock('postident/verification', array('getPostidentVerificationData'));
        $verificationMock->expects($this->once())
            ->method('getPostidentVerificationData')
            ->will($this->returnValue(array("identData" => array())));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $idCardMock = $this->getModelMock('postident/idCard_idCard10', array('checkAge'));
        $idCardMock->expects($this->once())
            ->method('checkAge')
            ->will($this->returnValue(false));
        $this->replaceByMock('model', 'postident/idCard_idCard10', $idCardMock);
        
        $this->assertFalse(Mage::getModel('postident/verification')->verifiyUserByAge());
    }
    
    public function testUserIsVerifiedSuccess()
    {
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        
        //Test success because of verification is not required
        $verificationMock->expects($this->once())
            ->method('isVerificationRequired')
            ->will($this->returnValue(false));
            
        $verificationMock->expects($this->never())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(array()));
            
        $verificationMock->expects($this->never())
            ->method('verifiyUserByAge')
            ->will($this->returnValue(array()));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertTrue(Mage::getModel('postident/verification')->userIsVerified());
        
        //Test success because of verification is required and the user has a valid age verification
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        $verificationMock->expects($this->once())
            ->method('isVerificationRequired')
            ->will($this->returnValue(true));
            
        $verificationMock->expects($this->once())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(true));
            
        $verificationMock->expects($this->once())
            ->method('verifiyUserByAge')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertTrue(Mage::getModel('postident/verification')->userIsVerified());
    }

    public function testUserIsVerifiedFailure()
    {
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        
        //Test failed because of verification is required and the user has no valid age verification
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        $verificationMock->expects($this->once())
            ->method('isVerificationRequired')
            ->will($this->returnValue(true));
            
        $verificationMock->expects($this->once())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(true));
            
        $verificationMock->expects($this->once())
            ->method('verifiyUserByAge')
            ->will($this->returnValue(false));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertFalse(Mage::getModel('postident/verification')->userIsVerified());
    }

    public function testUserIsVerifiedException()
    {
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        
        //Test failed because of verification is required and the user has no valid age verification
        $verificationMock = $this->getModelMock('postident/verification',
            array('isVerificationRequired', 'ageCheckIsRequired', 'verifiyUserByAge')
        );
        $verificationMock->expects($this->once())
            ->method('isVerificationRequired')
            ->will($this->throwException(new Exception));
            
        $verificationMock->expects($this->never())
            ->method('ageCheckIsRequired')
            ->will($this->returnValue(true));
            
        $verificationMock->expects($this->never())
            ->method('verifiyUserByAge')
            ->will($this->returnValue(false));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $helperMock = $this->getHelperMock('postident/data', array('log'));
        $helperMock->expects($this->once())
            ->method('log')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $this->assertFalse(Mage::getModel('postident/verification')->userIsVerified());
    }
    
    
    /**
     * test get postientdata from quote
     *
     * @test
     * @loadFixture quotes.yaml
     * @loadExpectation quotes.yaml
     */
    public function testGetPostidentVerificationDataFromQuote()
    {
        $quote = Mage::getModel('sales/quote')->load(1);
        $testVerificationData = unserialize($this->_getExpectations()->getVerificationdata());
        
        $verificationMock = $this->getModelMock('postident/verification', array('getQuote'));
        $verificationMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $this->assertTrue(is_array($testVerificationData));
        $this->assertEquals(
            $testVerificationData,
            Mage::getModel('postident/verification')->getPostidentVerificationDataFromQuote()
        );
        $this->assertNotEquals(
            'foo',
            Mage::getModel('postident/verification')->getPostidentVerificationDataFromQuote()
        );
    }
    
    /**
     * test get postientdata from customer
     *
     * @test
     * @loadFixture customer.yaml
     * @loadExpectation customer.yaml
     */
    public function testGetPostidentVerificationDataFromCustomer()
    {
        $customer = Mage::getModel('customer/customer')->load(1);
        $testVerificationData = unserialize($this->_getExpectations()->getVerificationdata());
        
        $verificationMock = $this->getModelMock('postident/verification', array('getCustomer'));
        $verificationMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertTrue(is_array($testVerificationData));
        $this->assertEquals(
            $testVerificationData,
            Mage::getModel('postident/verification')->getPostidentVerificationDataFromCustomer()
        );
        $this->assertNotEquals(
            'foo',
            Mage::getModel('postident/verification')->getPostidentVerificationDataFromCustomer()
        );
    }
    /**
     * test set postientdata on quote
     *
     * @test
     * @loadFixture quotes.yaml
     * @loadExpectation quotes.yaml
     */
    public function testSetPostidentVerificationDataToQuote()
    {
        $quoteMock = $this->getModelMock('sales/quote', array('_beforeSave'));
        $this->replaceByMock('model', 'sales/quote', $quoteMock);
        $quote = Mage::getModel('sales/quote')->load(2);
        $testVerificationData = unserialize($this->_getExpectations()->getVerificationdata());
        
        $verificationMock = $this->getModelMock('postident/verification', array('getQuote'));
        $verificationMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        //Test if verification data is empty before
        $this->assertFalse(is_array(unserialize($quote->getPostidentVerificationData())));
        
        $customerSessionMock = $this->getModelMock('core/session', array('renewSession', 'init', 'start'));
        $this->replaceByMock('model', 'core/session', $customerSessionMock);
        
        //Test if expected $testVerificationData was saved correctly to quote
        Mage::getModel('postident/verification')->setPostidentVerificationDataToQuote($testVerificationData["identData"]);
        $this->assertEquals(
            $testVerificationData,
            unserialize($quote->getPostidentVerificationData())
        );
        $quote = Mage::getModel('sales/quote')->load(2);
        
        $this->assertTrue(is_array(unserialize($quote->getPostidentVerificationData())));
        
        
        //Check case if quote is null
        $verificationMock = $this->getModelMock('postident/verification', array('getQuote'));
        $verificationMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->setExpectedException('Mage_Core_Exception');
        Mage::getModel('postident/verification')->setPostidentVerificationDataToQuote($testVerificationData["identData"]);
    }
    
    public function testGetPostidentVerificationDataTakeFromQuote()
    {
        $this->setUp();
        $postIdentDataFromQuote =  array( 
            'identData'  => array(
                'givenname'    => 'anton',
                'dateofbirth'  => '1980-05-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        $postIdentDataFromCustomer =  array( 
            'identData'  => array(
                'givenname'    => 'falk',
                'dateofbirth'  => '1970-01-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        
        //Test => Take verficiation data from quote
        $verificationMock = $this->getModelMock('postident/verification',
             array('getPostidentVerificationDataFromQuote', 'getPostidentVerificationDataFromCustomer')
        );
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromQuote')
            ->will($this->returnValue($postIdentDataFromQuote));
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromCustomer')
            ->will($this->returnValue($postIdentDataFromCustomer));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertEquals($postIdentDataFromQuote, Mage::getModel('postident/verification')->getPostidentVerificationData());
    }
    
    public function testGetPostidentVerificationDataTakeFromCustomer()
    {
        $this->setUp();
        $postIdentDataFromQuote =  array( 
            'identData'  => array(
                'givenname'    => 'anton',
                'dateofbirth'  => '1980-05-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        $postIdentDataFromCustomer =  array( 
            'identData'  => array(
                'givenname'    => 'falk',
                'dateofbirth'  => '1970-01-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );

        //Test => Take verficiation data from customer
        $this->store->resetConfig();
        $this->store->setConfig("postident/verification_criteria/checkout_verification", 0);
        
        $verificationMock = $this->getModelMock('postident/verification', array(
            'getPostidentVerificationDataFromQuote', 
            'getPostidentVerificationDataFromCustomer')
        );
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromQuote')
            ->will($this->returnValue(null));
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromCustomer')
            ->will($this->returnValue($postIdentDataFromCustomer));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertEquals($postIdentDataFromCustomer, Mage::getModel('postident/verification')->getPostidentVerificationData());
    }
    
    public function testGetPostidentVerificationDataTakeFromQuoteIfCustomerHasData()
    {
        $this->setUp();
        $postIdentDataFromQuote =  array( 
            'identData'  => array(
                'givenname'    => 'anton',
                'dateofbirth'  => '1980-05-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );
        $postIdentDataFromCustomer =  array( 
            'identData'  => array(
                'givenname'    => 'falk',
                'dateofbirth'  => '1970-01-10'
            ),
            'verificationDate' => Mage::getModel('core/date')->date("Y-m-d H:i:s")
        );

        //Test => Take verficiation data from quote if quote and customer both have verification data
        $this->store->resetConfig();
        $this->store->setConfig("postident/verification_criteria/checkout_verification", 0);
        
        $verificationMock = $this->getModelMock('postident/verification', array(
            'getPostidentVerificationDataFromQuote', 
            'getPostidentVerificationDataFromCustomer')
        );
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromQuote')
            ->will($this->returnValue($postIdentDataFromQuote));
        $verificationMock->expects($this->any($postIdentDataFromQuote))
            ->method('getPostidentVerificationDataFromCustomer')
            ->will($this->returnValue($postIdentDataFromCustomer));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertEquals($postIdentDataFromQuote, Mage::getModel('postident/verification')->getPostidentVerificationData());
        
    }
    
    public function testGetPostidentVerificationDataWithNoData()
    {
        //Test => Take no verficiation data because verification should be checked for every checkout
        $this->store->resetConfig();
        $this->store->setConfig("postident/verification_criteria/checkout_verification", 1);
        
        $verificationMock = $this->getModelMock('postident/verification',
             array('getPostidentVerificationDataFromQuote', 'getPostidentVerificationDataFromCustomer')
        );
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromQuote')
            ->will($this->returnValue(null));
        $verificationMock->expects($this->any())
            ->method('getPostidentVerificationDataFromCustomer')
            ->will($this->returnValue(''));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        $this->assertEquals(null, Mage::getModel('postident/verification')->getPostidentVerificationData());
    }
    
    /**
     * test case for min age only set global
     */
    public function testGetMinAgeWithGlobalMinAgeSet()
    {
        $configMock = $this->getModelMock('postident/verification', array(
            'getVerificationType',
            'getAgeVerification'
            )
        );
        $configMock->expects($this->any())
            ->method('getVerificationType')
            ->will($this->returnValue(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::GLOBAL_VALUE));
        $configMock->expects($this->any())
            ->method('getAgeVerification')
            ->will($this->returnValue(18));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        //test if no global and no product min age are set
        $this->assertEquals(18, Mage::getModel('postident/verification')->getMinAge());
    }
    
    /**
     * test case for min age only set on product
     */
    public function testGetMinAgeWithProductMinAgeSet()
    {
        $configMock = $this->getModelMock('postident/verification', array(
            'getVerificationType',
            )
        );
        $configMock->expects($this->any())
            ->method('getVerificationType')
            ->will($this->returnValue(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::PRODUCT_SPECIFIC));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'checkCart',
            )
        );
        
        $helperMock->expects($this->any())
            ->method('checkCart')
            ->will($this->returnValue(18));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        //test if no global and no product min age are set
        $this->assertEquals(18, Mage::getModel('postident/verification')->getMinAge());
        $this->assertNotEquals(0, Mage::getModel('postident/verification')->getMinAge());
        
    }
    /**
     * test case for min age on product AND global 
     */
    public function testGetMinAgeWithBothMinAgeSet()
    {
        $configMock = $this->getModelMock('postident/verification', array(
            'getVerificationType',
            'getAgeVerification'
            )
        );
        $configMock->expects($this->any())
            ->method('getVerificationType')
            ->will($this->returnValue(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::BOTH_VALUES));
        //return 18 for global min age
        $configMock->expects($this->any())
            ->method('getAgeVerification')
            ->will($this->returnValue(18));
        $this->replaceByMock('model', 'postident/config', $configMock);
        
        $helperMock = $this->getHelperMock('postident/data', array(
            'checkCart',
            )
        );
        //return 21 for global min age
        $helperMock->expects($this->any())
            ->method('checkCart')
            ->will($this->returnValue(21));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        $this->assertEquals(21, Mage::getModel('postident/verification')->getMinAge());
        $this->assertNotEquals(18, Mage::getModel('postident/verification')->getMinAge());
    }
}