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
 * DeutschePost_Postident_Test_Model_System_Config_ClientidTest
 * @author     André Herrn <andre.herrn@netresearch.de> 
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_System_Config_ClientidTest extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * @var Netresearch_Hermes_Model_Config
     */
    protected $systemConfigClientid;

    public function setUp()
    {
        $sessionMock = $this->getModelMock('adminhtml/session', array(
            'setCustomer', 'renewSession', 'init', 'start'
            )
         );
        $this->replaceByMock('model', 'adminhtml/session', $sessionMock);
        $this->systemConfigClientid = new DeutschePost_Postident_Model_System_Config_Clientid();
    }
    
    public function testAfterSaveSuccess()
    {
        //Expect no errors
        $clientMock = $this->getModelMock('postident/client', array(
            'sendCheckConnectRequest'
        ));
        $clientMock
            ->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->with(null, Mage::helper("postident")->getDomainUri(), $this->equalTo(null))
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->systemConfigClientid->_afterSave();
        
        $messages = Mage::getSingleton('adminhtml/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($error));
        $this->assertEquals(1, count($success));
        
        $this->assertEquals(
            'success: ' . Mage::helper('postident')->__('Client-ID and Domain-URI successfully validated by Check-Connect.'),
            current($success)->toString()
        );
    }
    
    public function testAfterSaveWithClientException()
    {
        $clientMock = $this->getModelMock('postident/client', array(
            'sendCheckConnectRequest'
        ));
        
        $clientMock
            ->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception));
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->systemConfigClientid->_afterSave();
        
        $messages = Mage::getSingleton('adminhtml/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
    }
    
    public function testAfterSaveWithClientResponseException()
    {
        $clientMock = $this->getModelMock('postident/client', array(
            'sendCheckConnectRequest'
        ));
        
        $clientMock
            ->expects($this->once())
            ->method('sendCheckConnectRequest')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Response_Exception));
        $this->replaceByMock('model', 'postident/client', $clientMock);
        $this->systemConfigClientid->_afterSave();
        
        $messages = Mage::getSingleton('adminhtml/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
    }
}