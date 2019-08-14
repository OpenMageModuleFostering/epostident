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
 * DeutschePost_Postident_Model_IdCard_IdCard10
 * @author     André Herrn <andre.herrn@netresearch.de> 
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_IdCard_IdCard10 extends DeutschePost_Postident_Model_IdCard_Abstract
{
    /**
     * @var boolean
     */
    public $allowAgeCheck = true;

    /**
     * checks if the age is above or the same as the age setup in the store config
     * 
     * @param array $verificationResponse
     * @return boolean
     */
    public function checkAge($verificationResponse)
    {
        if (true === is_array($verificationResponse)
            && true === array_key_exists('dateofbirth', $verificationResponse)) {
            return $this->checkAgeByBirthdate($verificationResponse["dateofbirth"]);
        }
    }
}