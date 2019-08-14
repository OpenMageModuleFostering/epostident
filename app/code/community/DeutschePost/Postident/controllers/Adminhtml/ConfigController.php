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
 * DeutschePost_Postident_Controllers_Adminhtml_ConfigController
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Adminhtml_ConfigController extends Mage_Adminhtml_Controller_Action
{

    /**
     * check if the current user is allowed to execute controller action
     * 
     * @return string
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                ->isAllowed('system/config/postident');
    }

    /**
     * check if the current user is allowed to see this section
     * 
     * @param string
     */
    protected function _checkSectionAllowed($section)
    {
        if (false == Mage::getSingleton('admin/session')
                ->isAllowed('system/config/postident/' . $section)) {
            $this->forward('denied');
        }
    }

    /**
     * calls the sendCheckConnectRequest Method with clientId and doaminUri
     * and returns the response as JSON
     */
    public function checkConnectAction()
    {
        try {
            Mage::getModel("postident/client")->sendCheckConnectRequest(
                $this->getRequest()->getParam('clientId'), $this->getRequest()->getParam('domainUri')
            );
            $result = array(
                'message' => Mage::helper('postident')->__("Check Connect Test successful."));
        } catch (DeutschePost_Postident_Model_Client_Exception $e) {
            $result = array('message' => $e->getMessage());
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $e) {
            $result = array('message' => $e->getMessage());
        }
        $this->getResponse()->setBody(
            Zend_Json::encode($result)
        );
    }
}
