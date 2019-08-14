<?php /**
 * @category   DeutschePost Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Test_Controller_CartControllerTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Controller_CartControllerTest 
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function testBackAction()
    {
        $clientHelperMock = $this->getHelperMock('postident/client', array(
            'validateRedirectResponse',
            'saveIdentDataToQuote'
        ));
        $clientHelperMock->expects($this->any())
            ->method('validateRedirectResponse')
            ->will($this->returnValue(null));
        
        $clientHelperMock->expects($this->once())
            ->method('saveIdentDataToQuote')
            ->will($this->returnValue(null));
        
        $this->replaceByMock('helper', 'postident/client', $clientHelperMock);
        
        $clientModelMock = $this->getModelMock('postident/client', array(
            'sendAccessTicketRequest',
            'sendIdentDataRequest'
        ));
        $clientModelMock->expects($this->any())
            ->method('sendAccessTicketRequest')
            ->will($this->returnValue(null));
        
         $clientModelMock->expects($this->any())
            ->method('sendIdentDataRequest')
            ->will($this->returnValue(null));
       
        $this->replaceByMock('model', 'postident/client', $clientModelMock);
        
        $verificationMock = $this->getModelMock('postident/verification', array('userIsVerified'));
        $verificationMock->expects($this->any())
            ->method('userIsVerified')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $this->dispatch('postident/cart/back/code/foo');
        $this->assertRequestRoute('postident/cart/back');
        $this->assertRedirectTo('checkout/onepage', array('_store' => ''));
        
        $messages = Mage::getSingleton('core/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $notice   = $messages->getItemsByType(Mage_Core_Model_Message::NOTICE);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($notice));
        $this->assertEquals(0, count($error));
        $this->assertEquals(1, count($success));
        $this->reset();
    }
    
    public function testBackActionWithHelperClientException()
    {
        Mage::getSingleton('core/session')->getMessages(true);
        $this->reset();
        $helperMock = $this->getHelperMock('postident/client', array(
            'validateRedirectResponse'
        ));
        $helperMock->expects($this->any())
            ->method('validateRedirectResponse')
            ->will($this->throwException(new DeutschePost_Postident_Helper_Client_Exception));
        $this->replaceByMock('helper', 'postident/client', $helperMock);
        $this->dispatch('postident/cart/back/code/foo');
        $this->assertRequestRoute('postident/cart/back');
        $this->assertRedirectTo('checkout/cart', array('_store' => ''));
        
        $messages = Mage::getSingleton('core/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $notice   = $messages->getItemsByType(Mage_Core_Model_Message::NOTICE);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($notice));
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
        $this->reset();

    }
    
    public function testBackActionWithModelClientException()
    {
        $helperMock = $this->getHelperMock('postident/client', array(
            'validateRedirectResponse',
        ));
        $helperMock->expects($this->any())
            ->method('validateRedirectResponse')
            ->will($this->throwException(new DeutschePost_Postident_Model_Client_Exception));
        
        $this->replaceByMock('helper', 'postident/client', $helperMock);
        $this->dispatch('postident/cart/back/code/foo');
        $this->assertRequestRoute('postident/cart/back');
        $this->assertRedirectTo('checkout/cart', array('_store' => ''));
        
        $messages = Mage::getSingleton('core/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $notice    = $messages->getItemsByType(Mage_Core_Model_Message::NOTICE);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($notice));
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
        $this->reset();
    }
    
    public function testBackActionWithStandartException()
    {
        $helperMock = $this->getHelperMock('postident/client', array(
            'validateRedirectResponse'
        ));
        $helperMock->expects($this->any())
            ->method('validateRedirectResponse')
            ->will($this->throwException(new Exception));
        $this->replaceByMock('helper', 'postident/client', $helperMock);
        $this->dispatch('postident/cart/back/code/foo');
        $this->assertRequestRoute('postident/cart/back');
        $this->assertRedirectTo('checkout/cart', array('_store' => ''));
        
        $messages = Mage::getSingleton('core/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $notice   = $messages->getItemsByType(Mage_Core_Model_Message::NOTICE);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($notice));
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
        $this->reset();
    }

      
    public function testBackActionFailedBecauseOfVerification()
    {
        $clientHelperMock = $this->getHelperMock('postident/client', array(
            'validateRedirectResponse',
            'saveIdentDataToQuote'
        ));
        $clientHelperMock->expects($this->any())
            ->method('validateRedirectResponse')
            ->will($this->returnValue(null));
        
        $clientHelperMock->expects($this->once())
            ->method('saveIdentDataToQuote')
            ->will($this->returnValue(null));
        
        $this->replaceByMock('helper', 'postident/client', $clientHelperMock);
        
        $clientModelMock = $this->getModelMock('postident/client', array(
            'sendAccessTicketRequest',
            'sendIdentDataRequest'
        ));
        $clientModelMock->expects($this->any())
            ->method('sendAccessTicketRequest')
            ->will($this->returnValue(null));
        
        $clientModelMock->expects($this->any())
            ->method('sendIdentDataRequest')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'postident/client', $clientModelMock);
        
        $verificationMock = $this->getModelMock('postident/verification', array('userIsVerified'));
        $verificationMock->expects($this->any())
            ->method('userIsVerified')
            ->will($this->returnValue(false));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        $this->dispatch('postident/cart/back/code/foo');
        $this->assertRequestRoute('postident/cart/back');
        $this->assertRedirectTo('checkout/cart', array('_store' => ''));
        
        $messages = Mage::getSingleton('core/session')->getMessages(true);
        $success  = $messages->getItemsByType(Mage_Core_Model_Message::SUCCESS);
        $notice   = $messages->getItemsByType(Mage_Core_Model_Message::NOTICE);
        $error    = $messages->getItemsByType(Mage_Core_Model_Message::ERROR);
        
        $this->assertEquals(0, count($notice));
        $this->assertEquals(1, count($error));
        $this->assertEquals(0, count($success));
        
        $this->assertEquals(
            Mage::helper('postident')->__('The identification was passed succesfully but the verification to enter the checkout failed.'),
            $error[0]->getCode()
        );
        
        $this->reset();
    }
}
