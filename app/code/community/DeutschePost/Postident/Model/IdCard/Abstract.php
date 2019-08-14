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
class DeutschePost_Postident_Model_IdCard_Abstract extends Varien_Object
{
    /**
     * Checks if ID-Card has Age-Check-Verification
     */
    public function allowAgeCheck()
    {
       return $this->allowAgeCheck;
    }
    
    /**
     * checks if the age is above or the same as the age setup in the store config
     * 
     * @param string $verificationResponse
     * @return boolean
     */
    public function checkAgeByBirthdate($dateOfBirth, $currentDate = null)
    {
        if (is_null($currentDate)) {
            $currentDate = Mage::getModel('core/date')->date("Y/m/d");
        }
        
        if (($currentDate - $dateOfBirth)
            >=  Mage::getModel('postident/verification')->getMinAge())
            return true;
        else
            return false;
    }
}