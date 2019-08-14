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
 * DeutschePost_Postident_Test_Model_Client_Response_ExceptionTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_Client_Response_ExceptionTest extends EcomDev_PHPUnit_Test_Case
{
    public function testCheckConnectError()
    {
        $clientResponseException = new DeutschePost_Postident_Model_Client_Response_Exception();
        $helperMock = $this->getHelperMock('postident/data', array(
            'log',
            '__'
        ));
        $helperMock
            ->expects($this->once())
            ->method('log')
            ->will($this->returnValue(null));
        
        $helperMock->expects($this->once())
            ->method('__')
            ->will($this->returnValue(null));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        $this->setExpectedException(
            'DeutschePost_Postident_Model_Client_Response_Exception'
        );
        $clientResponseException->checkConnectError("dummy message");
    }
}