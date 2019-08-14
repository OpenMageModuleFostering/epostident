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
 * DeutschePost_Postident_Model_IdCard_Abstract
 * @author     André Herrn <andre.herrn@netresearch.de> 
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_IdCard_AbstractTest extends EcomDev_PHPUnit_Test_Case
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
    
    /**
     * Check if all ID-Cards have a correct class 
     */
    public function testCheckAllIdCardClasses()
    {
        foreach ($this->config->getAllIdcards() as $idCardConfig) {
            $idCardInstance = $this->config->getIdCardInstance($idCardConfig["code"]);
            $this->assertTrue("object" == gettype($idCardInstance));
            $this->assertTrue($idCardInstance instanceOf DeutschePost_Postident_Model_IdCard_Abstract);
        }
    }
    
    public function testAllowAgeCheck()
    {
        /*
         * Currently all selectable ID-Cards support AgeCheck
         */
        foreach ($this->config->getAllIdcards() as $idCardConfig) {
            $idCardInstance = $this->config->getIdCardInstance($idCardConfig["code"]);
            $this->assertTrue($idCardInstance->allowAgeCheck());
        }
        
        $idCardInstance = $this->config->getIdCardInstance(40);
        $this->assertFalse($idCardInstance->allowAgeCheck());
    }
    
    /**
     * test if CheckAge returns true and false correctly based on dateofbirth
     */
    public function testcheckAgeByBirthdate()
    {   
        $verificationModelMock = $this->getModelMock('postident/config', array(
            'getMinAge'
        ));
        $verificationModelMock->expects($this->any())
            ->method('getMinAge')
            ->will($this->returnValue(18));
        $this->replaceByMock('model', 'postident/verification', $verificationModelMock);
        $this->assertTrue(Mage::getModel('postident/IdCard_Abstract')->checkAgeByBirthdate( 
             '1980-05-10 00:00:00.0',
            '2010-05-10 00:00:00.0'
        ));
        $this->assertFalse(Mage::getModel('postident/IdCard_Abstract')->checkAgeByBirthdate( 
             '1998-05-10 00:00:00.0',
            '1999-05-10 00:00:00.0'
        ));
        //assertion for 17y and 3xx days
        $this->assertFalse(Mage::getModel('postident/IdCard_Abstract')->checkAgeByBirthdate( 
             '1980-01-01 00:00:00.0',
            '1997-12-31 00:00:00.0'
        ));
        //assertion for 18y and 1 day
        $this->assertTrue(Mage::getModel('postident/IdCard_Abstract')->checkAgeByBirthdate( 
             '1980-01-01 00:00:00.0',
            '1998-01-02 00:00:00.0'
        ));
    }
}