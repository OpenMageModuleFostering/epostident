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
 * DeutschePost_Postident_Model_System_Config_Source_Verificationtype
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class DeutschePost_Postident_Model_System_Config_Source_Verificationtype
{
   
    const GLOBAL_VALUE = 'global';
    const PRODUCT_SPECIFIC = 'product_specific';
    const BOTH_VALUES = 'both';
    /**
     * values for type of verification
     *
     * @return array $options
     */
    public function toOptionArray()
    {
        $options = array(
            array('value'  => self::GLOBAL_VALUE,
            'label'  => Mage::helper('postident/data')->__('global')),
            array('value'  => self::PRODUCT_SPECIFIC,
            'label'  => Mage::helper('postident/data')->__('product specific')),
            array('value'  => self::BOTH_VALUES,
            'label'  => Mage::helper('postident/data')->__('both')),
            
        );
        return $options;
    }
}