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
 * DeutschePost_Postident_Model_System_Config_Source_Idcards
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */
class DeutschePost_Postident_Model_System_Config_Source_Idcards
{
    /**
     * Get all possible ID-Cards
     *
     * @return array $idcards
     */
    public function toOptionArray()
    {
        $idcards = array();
        foreach (Mage::getModel("postident/config")->getAllIdcards() as $idcard):
            $idcards[$idcard["code"]] = array(
                'label' => $idcard["title"],
                'value' => $idcard["code"]
            );
        endforeach;
        return $idcards;
    }
}