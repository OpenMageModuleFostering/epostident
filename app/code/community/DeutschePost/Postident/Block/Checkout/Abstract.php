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
 * DeutschePost_Postident_Block_Checkout_Abstract
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Block_Checkout_Abstract extends Mage_Core_Block_Template
{
   /**
    * get postident data from quote or customer
    * 
    * 
    * @return array
    */
    public function getPostidentData()
    {
        $postidentData = Mage::getModel('postident/verification')->getPostidentVerificationData();
        $dateString = $postidentData['identData']['dateofbirth'];
        $postidentData['identData']['dateofbirth'] =  strtok($dateString," ");
        unset($postidentData['identData']['verification_date']);
        return $postidentData['identData'];
    }
    
   /**
    * get postident data from quote or customer
    * and return it as Json
    * 
    * @return string
    */
    public function getPostidentDataAsJson()
    {
        return Mage::helper('core')->jsonEncode($this->getPostidentData());
    }
}