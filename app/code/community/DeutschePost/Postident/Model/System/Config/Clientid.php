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
 * DeutschePost_Postident_Model_System_Config_Clientid
 * @author     André Herrn <andre.herrn@netresearch.de> 
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_System_Config_Clientid extends Mage_Core_Model_Config_Data
{
    /**
     * Check if Client-ID and Domain-URI are valid
     *
     * @return void
     */
    public function _afterSave()
    {
        $params = Mage::app()->getRequest()->getParams();
        $checkConnectUrl = null;
        if (true === isset($params["groups"]["gateway_urls"]["fields"]["check_connect_url"]["value"])) {
            $checkConnectUrl = $params["groups"]["gateway_urls"]["fields"]["check_connect_url"]["value"];
        }
        
        try {
            Mage::getModel("postident/client")->sendCheckConnectRequest(
                $this->getValue(),
                Mage::helper("postident")->getDomainUri(),
                $checkConnectUrl //Get current POST-Check Connect Url
            );
            
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper("postident")->__("Client-ID and Domain-URI successfully validated by Check-Connect.")
            );
            
            parent::_beforeSave();
        } catch (DeutschePost_Postident_Model_Client_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $e->getMessage()
            );
        } catch (DeutschePost_Postident_Model_Client_Response_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $e->getMessage()
            );
        }
    }
}