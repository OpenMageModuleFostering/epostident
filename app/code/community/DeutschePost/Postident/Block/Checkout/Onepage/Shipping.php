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
 * DeutschePost_Postident_Block_Checkout_Onepage_Shipping
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Block_Checkout_Onepage_Shipping
    extends DeutschePost_Postident_Block_Checkout_Abstract
{

    /**
     * Internal constructor, that is called from real constructor.
     *
     * @return void
     */
    protected function _construct()
    {   
        parent::_construct();
        $this->setTemplate('postident/checkout/onepage/shipping.phtml');
    }
}
