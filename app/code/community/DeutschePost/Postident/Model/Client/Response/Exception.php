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
 * DeutschePost_Postident_Model_Client_Response_Exception
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Client_Response_Exception extends Exception
{
    /**
     * Basic method to throw client response exceptions
     * 
     * @param string $message
     *
     * @throws DeutschePost_Postident_Model_Client_Response_Exception
     */
    public static function responseError($message)
    {
        $helper = Mage::helper("postident/data");
        $helper->log($message);
        
        //throw DeutschePost_Postident_Model_Client_Response_Exception
        throw new self(
            $helper->__(
                'Client-Response Error: %s',
                 $message
            )
        );
    }
    
    /**
     * Build check connect error message
     * 
     * @param string $message
     *
     * @return void
     */
    public static function checkConnectError($message)
    {
        self::responseError($message);
    }
    
    /**
     * Build check access ticket error message
     * 
     * @param string $message
     *
     * @return void
     */
    public static function accessTicketError($message)
    {
        self::responseError($message);
    }
    
    /**
     * Build throwValidateXMlResponse error message
     * 
     * @param string $message
     *
     * @return void
     */
    public static function throwValidateXMlResponseError($message)
    {
       self::responseError($message);
    }
}