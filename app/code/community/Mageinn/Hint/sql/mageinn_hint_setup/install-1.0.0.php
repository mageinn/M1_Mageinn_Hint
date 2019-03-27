<?php
/**
 * Mageinn_Hint extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mageinn
 * @package     Mageinn_Hint
 * @copyright   Copyright (c) 2016 Mageinn. (http://mageinn.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'eav_attribute_hint'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mageinn_hint/hint'))
    ->addColumn('attribute_hint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Hint Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Value')
    ->addIndex('IDX_EAV_ATTRIBUTE_HINT_ATTRIBUTE_ID', array('attribute_id'))
    ->addIndex('IDX_EAV_ATTRIBUTE_HINT_STORE_ID', array('store_id'))
    ->addIndex('IDX_EAV_ATTRIBUTE_HINT_ATTRIBUTE_ID_STORE_ID', array('attribute_id','store_id')) 
    ->addForeignKey('FK_EAV_ATTRIBUTE_HINT_ATTRIBUTE_ID_EAV_ATTRIBUTE_ATTRIBUTE_ID',
                'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey('FK_EAV_ATTRIBUTE_HINT_STORE_ID_CORE_STORE_STORE_ID',
                'store_id', $installer->getTable('core/store'), 'store_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Hint');

$installer->getConnection()->createTable($table);


/**
 * Create table 'eav_attribute_option_hint'
 */
$table2 = $installer->getConnection()
    ->newTable($installer->getTable('mageinn_hint/option_hint'))
    ->addColumn('attribute_option_hint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Option Hint Id')
     ->addColumn('attr_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Value')
    ->addIndex('IDX_EAV_ATTRIBUTE_OPTION_HINT_ATTR_ID', array('attr_id'))
    ->addIndex('IDX_EAV_ATTRIBUTE_OPTION_HINT_OPTION_ID', array('option_id'))
    ->addIndex('IDX_EAV_ATTRIBUTE_OPTION_HINT_STORE_ID', array('store_id'))
    ->addForeignKey('FK_EAV_ATTR_OPT_HINT_OPT_ID_EAV_ATTR_OPT_OPT_ID',
                'option_id', $installer->getTable('eav/attribute_option'), 'option_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey('FK_EAV_ATTRIBUTE_OPTION_HINT_STORE_ID_CORE_STORE_STORE_ID',
                'store_id', $installer->getTable('core/store'), 'store_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Option Hint');

$installer->getConnection()->createTable($table2);
$installer->endSetup();