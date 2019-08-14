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
class DeutschePost_Postident_Test_Controller_Adminhtml_PermissionDeniedControllerTest
    extends DeutschePost_Postident_Test_Controller_Adminhtml_AbstractControllerTest
{
    public function setUp()
    {
        $this->reset();
        $this->_fakeLogin();
        parent::setUp();
    }
    
    protected function _allowGlobalAccess()
    {
        $session = $this->getModelMock('admin/session', array('isAllowed', 'renewSession'));
        $session->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnCallback(array($this, 'fakePermission')));
            /*
             * uncommented:
             * ->will($this->returnValue(true));
             * 
             * This is the difference to original 
             * DeutschePost_Postident_Test_Controller_Adminhtml_AbstractControllerTest::_allowGlobalAccess
             */
//            
        $this->replaceByMock('singleton', 'admin/session', $session);
    }
    
    public static function fakePermission($path)
    {
        return 'system/config/postident' === $path;
    }

    public function testCheckSectionAllowed()
    {
        $this->dispatch('postident/adminhtml_config/checkConnect');
        //Todo
        $this->markTestIncomplete('Todo');
        //Currently no redirect is detected
        //$this->assertRedirect();
        //Mage::getSingleton('adminhtml/session')->getMessages(true);
    }
}