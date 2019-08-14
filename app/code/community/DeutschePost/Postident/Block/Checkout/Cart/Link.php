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
 * DeutschePost_Postident_Block_Checkout_Cart_Link
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Block_Checkout_Cart_Link extends Mage_Core_Block_Template
{
    /**
     * gets the redirect link from model and passing it to template
     * 
     * @return string
     */
    public function getPostidentLink()
    {
        return Mage::helper('postident/client')->buildRedirectLink();
    }
    
    /**
     * gets the selected id_card from condig model and passing it to template
     * 
     * @return string
     */
    public function getIdCard()
    {
        return Mage::getModel('postident/config')->getSelectedIdCard();
    }
}