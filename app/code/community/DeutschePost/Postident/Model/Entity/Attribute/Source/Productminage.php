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
 * DeutschePost_Postident_Model_Entity_Attribute_Source_Productminage
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class DeutschePost_Postident_Model_Entity_Attribute_Source_Productminage
	extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const USE_DEFAULT = 0;
    
   /**
	 * Retrieve all options array
	 *
	 * @return array
	 */
	public function getAllOptions()
	{
		if (is_null($this->_options)) {
			$this->_options = array(
				array(
					'label' => Mage::helper('postident')->__('No age restriction'),
					'value' =>  self::USE_DEFAULT,
				),
				array(
					'label' => Mage::helper('postident')->__('16 years'),
					'value' =>  16,
				),
                array(
					'label' => Mage::helper('postident')->__('18 years'),
					'value' =>  18,
				),
			);
		}
		return $this->_options;
	}
	
	/**
	 * Bugfix for Magento 1.3 - do not return the option array entry, only the label.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function getOptionText($value)
	{
		$option = parent::getOptionText($value);
		if (is_array($option) && isset($option['label']))
		{
			$option = $option['label'];
		}
		return $option;
	}
    
}