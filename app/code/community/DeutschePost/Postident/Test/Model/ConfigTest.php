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
 * DeutschePost_Postident_Test_Model_ConfigTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_ConfigTest extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * @var DeutschePost_Postident_Model_Config
     */
    protected $config;

    public function setUp()
    {
        $this->store = Mage::app()->getStore(0)->load(0);
        $this->config = Mage::getModel('postident/config');
    }

    public function testType()
    {
        $this->assertInstanceOf(
            'DeutschePost_Postident_Model_Config', Mage::getModel('postident/config')
        );
    }

    public function testIsEnabled()
    {
        $path = 'postident/general/active';
        $this->store->resetConfig();
        $this->store->setConfig($path, 1);
        $this->assertTrue($this->config->isEnabled());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertFalse($this->config->isEnabled());
    }

    public function testIsLoggingEnabled()
    {
        $path = 'postident/general/logging_enabled';
        $this->store->resetConfig();
        $this->store->setConfig($path, 1);
        $this->assertTrue($this->config->isLoggingEnabled());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertFalse($this->config->isLoggingEnabled());
    }


    public function testGetClientId()
    {
        $path = 'postident/master_data/client_id';
        $this->store->resetConfig();
        $this->store->setConfig($path, 'foo');
        $this->assertEquals('foo', $this->config->getClientId());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertNotEquals('foo', $this->config->getClientId());
    }

    public function testGetClientSecret()
    {
        $path = 'postident/master_data/client_secret';
        $this->store->resetConfig();
        $this->store->setConfig($path, 'foo');
        $this->assertEquals('foo', $this->config->getClientSecret());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertNotEquals('foo', $this->config->getClientSecret());
    }

    public function testGetAllIdcards()
    {
        $allIdCards = $this->config->getAllIdcards();
        $this->assertTrue(is_array($allIdCards));
        $this->assertGreaterThanOrEqual(3, count($allIdCards)); //We start with 3 id-cards: 10,120,130
        $allIdCards = array_shift($allIdCards);
        $this->assertArrayHasKey('code', $allIdCards);
        $this->assertArrayHasKey('title', $allIdCards);

    }
    
    public function testgetSelectedIdCard()
    {
        $path ='postident/idcard/testmode';
        $this->store->resetConfig();
        $this->store->setConfig($path, 1);
        $this->assertEquals(1304, $this->config->getSelectedIdCard());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertNotEquals(1304, $this->config->getSelectedIdCard());
        
        $path ='postident/idcard/number';
        $this->store->setConfig($path, 120);
        $this->assertEquals(120, $this->config->getSelectedIdCard());
    }
    
    public function testIsTestMode()
    {
        $path ='postident/idcard/testmode';
        $this->store->resetConfig();
        $this->store->setConfig($path, 1);
        $this->assertEquals(true, $this->config->isTestMode());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertEquals(false ,$this->config->isTestMode());
    }
    
    public function testGetAgeVerification()
    {
        $path ='postident/verification_criteria/age_verification';
        $this->store->resetConfig();
        $this->store->setConfig($path, 15);
        $this->assertEquals(15, $this->config->getAgeVerification());
        $this->store->resetConfig();
        $this->store->setConfig($path, 23);
        $this->assertNotEquals('foo' ,$this->config->getAgeVerification());
    }
    
    public function testGetPostidentUrl()
    {
        $path = 'postident/gateway_urls/postident_url';
        $this->store->resetConfig();
        $this->store->setConfig($path, 'www.test.de');
        $this->assertEquals('www.test.de', $this->config->getPostidentUrl());
        $this->store->resetConfig();
        $this->store->setConfig($path, 'foo');
        $this->assertNotEquals('www.test.de',$this->config->getPostidentUrl());
    }
    

    public function testGetIdCardInstance()
    {
        //Take ID Card 10 as example
        $idCardInstance = $this->config->getIdCardInstance(10);
        $this->assertTrue($idCardInstance instanceOf DeutschePost_Postident_Model_IdCard_Abstract);
    }
    
    public function testGetSelectedIdCardInstance()
    {
        $pathSelectedIdCard ='postident/idcard/number';
        $pathTestMode ='postident/idcard/testmode';
        $this->store->resetConfig();
        $this->store->setConfig($pathSelectedIdCard, 10);
        $this->store->setConfig($pathTestMode, 0);
        $this->assertTrue($this->config->getSelectedIdCardInstance() instanceOf DeutschePost_Postident_Model_IdCard_IdCard10);
        
        $this->store->resetConfig();
        $this->store->setConfig($pathSelectedIdCard, 120);
        $this->store->setConfig($pathTestMode, 0);
        $this->assertTrue($this->config->getSelectedIdCardInstance() instanceOf DeutschePost_Postident_Model_IdCard_IdCard120);
        
        $this->store->resetConfig();
        $this->store->setConfig($pathTestMode, 1);
        $this->assertTrue($this->config->getSelectedIdCardInstance() instanceOf DeutschePost_Postident_Model_IdCard_IdCard1304);
    }
    
    public function testGetIdentDataUrl()
    {
        $path = 'postident/gateway_urls/identdata_url';
        $this->store->resetConfig();
        $this->store->setConfig($path, 'www.test.de');
        $this->assertEquals('www.test.de', $this->config->getIdentDataUrl());
        $this->store->resetConfig();
        $this->store->setConfig($path, 'foo');
        $this->assertNotEquals('www.test.de',$this->config->getIdentDataUrl());
    }
    
    public function testGetVerificationType()
    {
        $path = 'postident/verification_criteria/verification_type';
        
        //Assert Default
        $this->assertEquals(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::GLOBAL_VALUE, $this->config->getVerificationType());
        
        $this->store->resetConfig();
        $this->store->setConfig($path, DeutschePost_Postident_Model_System_Config_Source_Verificationtype::PRODUCT_SPECIFIC);
        $this->assertEquals(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::PRODUCT_SPECIFIC, $this->config->getVerificationType());
        
        $this->store->resetConfig();
        $this->store->setConfig($path, DeutschePost_Postident_Model_System_Config_Source_Verificationtype::BOTH_VALUES);
        $this->assertEquals(DeutschePost_Postident_Model_System_Config_Source_Verificationtype::BOTH_VALUES ,$this->config->getVerificationType());
    }
    
   public function testGetAddressDataUsage()
   {
        $path = 'postident/checkout/use_address_data';
        $this->store->resetConfig();
        $this->store->setConfig($path, true);
        $this->assertEquals(true, $this->config->getAddressDataUsage());
        $this->store->resetConfig();
        $this->store->setConfig($path, false);
        $this->assertEquals(false,$this->config->getAddressDataUsage());
   }
   
    public function testVerifyForEveryCheckout()
    {
        $path ='postident/verification_criteria/checkout_verification';
        $this->store->resetConfig();
        $this->store->setConfig($path, 1);
        $this->assertEquals(true, $this->config->verifyForEveryCheckout());
        $this->store->resetConfig();
        $this->store->setConfig($path, 0);
        $this->assertEquals(false ,$this->config->verifyForEveryCheckout());
    }
}