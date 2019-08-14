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
 * DeutschePost_Postident_Test_Controller_Adminhtml_ConfigControllerTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Controller_Adminhtml_ConfigControllerTest
    extends DeutschePost_Postident_Test_Controller_Adminhtml_AbstractControllerTest
{
    public function setUp()
    {
        $this->reset();
        $this->_fakeLogin();
        parent::setUp();
    }
    
    /**
     * Logged in to Magento with fake user to test an adminhtml controllers
     */
    /**
     * Test whether fake user successfully logged in
     */
    public function testLoggedIn()
    {
        $this->assertTrue(Mage::getSingleton('admin/session')->isLoggedIn());
    }

    /**
     * Test whether logged user is fake
     */
    public function testLoggedUserIsFakeUser()
    {
        $this->assertEquals(Mage::getSingleton('admin/session')->getUser()->getId(), self::FAKE_USER_ID);
    }
    
    /**
     * Test check connect button controller
     */
    public function testCheckConnectActionSuccess()
    {
        $checkConnectResponse = array("status" => "200", "message" => "toll");
        
        $clientMock = $this->getModelMock('postident/client', array('sendCheckConnectRequest'));
        $clientMock->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->will($this->returnValue($checkConnectResponse)); //sendCheckConnectRequest returns an array with status and message
        
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->dispatch('postident/adminhtml_config/checkConnect');
        $this->assertRequestRoute('postident/adminhtml_config/checkConnect');
        
        $this->assertEquals(
            $this->getResponse()->getOutputBody(),
            Zend_Json::encode(
                array('message' => Mage::helper('postident')->__("Check Connect Test successful."))
            )
        );
    }
    
    /**
     * Test check connect button controller
     */
    public function testCheckConnectActionWithClientException()
    {
        $clientMock = $this->getModelMock('postident/client', array('sendCheckConnectRequest'));
        $clientMock->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception)); //sendCheckConnectRequest returns an array with status and message
        
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->dispatch('postident/adminhtml_config/checkConnect');
        $this->assertRequestRoute('postident/adminhtml_config/checkConnect');
        
        $this->assertNotEquals(
            $this->getResponse()->getOutputBody(),
            Zend_Json::encode(
                array('message' => Mage::helper('postident')->__("Check Connect Test successful."))
            )
        );
    }
    
    /**
     * Test check connect button controller
     */
    public function testCheckConnectActionWithClientResponseException()
    {
        $clientMock = $this->getModelMock('postident/client', array('sendCheckConnectRequest'));
        $clientMock->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Response_Exception)); //sendCheckConnectRequest returns an array with status and message
        
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->dispatch('postident/adminhtml_config/checkConnect');
        $this->assertRequestRoute('postident/adminhtml_config/checkConnect');
        
        $this->assertNotEquals(
            $this->getResponse()->getOutputBody(),
            Zend_Json::encode(
                array('message' => Mage::helper('postident')->__("Check Connect Test successful."))
            )
        );
    }
}