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
 * DeutschePost_Postident_Block_Adminhtml_System_Config_Checkconnect_Button
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class DeutschePost_Postident_Block_Adminhtml_System_Config_Checkconnect_Button 
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * add button block to the rendered html
     * 
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper('adminhtml')->getUrl(
            'postident/adminhtml_config/checkConnect',
            array('domainUri' => Mage::helper('postident')->getDomainUri())
        ); 
        $url = rtrim($url,'/');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Check Connect')
            ->setOnClick("sentCheckConnectRequest('{$url}')")
            ->toHtml();

        return $html;
    }
}
?>
