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
 * DeutschePost_Postident_Sql_Postident_Setup
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();

// add additional data to quote table
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_quote'), 
        'postident_verification_data', 
        'text NULL DEFAULT NULL'
);

//Add new product attribute epostident_minage for minimum age
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'epostident_minage', array(
    'group'                       => 'General',
    'label'                       => 'E-POSTIDENT Minimum age',
    'type'                        => 'int',
    'input'                       => 'text',
    'source'                      => '',
    'global'                      => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'                    => false,
    'default'                     => DeutschePost_Postident_Model_Entity_Attribute_Source_Productminage::USE_DEFAULT,
    'user_defined'                => 0,
    'apply_to'                    => array(),
    'used_in_product_listing'     => 1,
    'is_configurable'             => 0,
    'filterable_in_search'        => 0,
    'used_for_price_rules'        => 0,
));

// add additional data to customer table
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'postident_verification_data', array(
    'type'          => 'text',
    'label'         => 'postident verification data',
    'required'      => false,
    'note'          => 'JSON Postident Verification Data',
));

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'postident_verification_data',
 '999'  //sort_order
);

$installer->endSetup();