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
 * DeutschePost_Postident_Block_Adminhtml_Customer_Edit_Tab_Verificationdata
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */
class DeutschePost_Postident_Block_Adminhtml_Customer_Edit_Tab_Verificationdata
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('postident/customer/verificationdata.phtml');
    }
   
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('postident')->__('E-POSTIDENT Data');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('postident')->__('Verificationdata');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }
    
    /**
     * Get unserialized postident verification data
     *
     * @return array
     */
    public function getPostidentVerificationData()
    {
        return unserialize(Mage::registry('current_customer')->getPostidentVerificationData());
    }
}