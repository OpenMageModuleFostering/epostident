<?php
/**
 * @category   Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Test_Model_System_Config_Source_IdcardsTest
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Model_System_Config_Source_IdcardsTest extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testSourceClassExists()
    {
        $this->assertTrue(class_exists('DeutschePost_Postident_Model_System_Config_Source_Idcards'));
        return new DeutschePost_Postident_Model_System_Config_Source_Idcards();
    }
    
    /**
     * @depends testSourceClassExists
     */
    public function testToOptionArray(DeutschePost_Postident_Model_System_Config_Source_Idcards $sourceIdcards)
    {
        $this->assertTrue(method_exists($sourceIdcards, "toOptionArray"));
        $this->assertTrue(is_array($sourceIdcards->toOptionArray()));
        
        $options = $sourceIdcards->toOptionArray();
        $this->assertNotEmpty($options);
        $this->assertTrue(
            count($options) > 0,
            sprintf(
                "Available ID-Cards calculated (%s) num is not bigger 0",
                count($options)
            )
        );
    }
}