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
 * DeutschePost_Postident_Model_Client_Exception
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Model_Client_Exception extends Zend_Http_Client_Adapter_Exception
{
    /**
     * Basic method to throw client exceptions
     * 
     * @param string $message
     *
     * @throws DeutschePost_Postident_Model_Client_Exception
     */
    public static function clientError($message)
    {
        $helper = Mage::helper("postident/data");
        $helper->log($message);
        
        //throw DeutschePost_Postident_Model_Client_Exception
        throw new self(
            $helper->__(
                'Client-Exception: s%',
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
    public static function connectError($message)
    {
        self::clientError(sprintf(
            'Unable to connect to E-POSTIDENT Webservice. Http-Client Error-Message: %s',
             $message
           )
        );
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
        self::clientError(sprintf(
            'Error during Access-Ticket-Request.Http-Client Error-Message: %s',
             $message
           )
        );
    }   
    
    /**
     * Build identData error message
     * 
     * @param string $message
     *
     * @return void
     */
    public static function identDataError($message)
    {
        self::clientError(sprintf(
            'Error while requesting ident data. Http-Client Error-Message: %s',
             $message
            )
        );
    }
}