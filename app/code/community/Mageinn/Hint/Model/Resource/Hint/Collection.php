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
 * Hint collection model
 *
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */
class Mageinn_Hint_Model_Resource_Hint_Collection
    extends Mage_Core_Model_Resource_DB_Collection_Abstract
{
    /**
     * constructor function
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageinn_hint/hint');
    }
    
    /**
     * Add Current Store filter
     *
     * @return Mageinn_Hint_Model_Resource_Hint_Collection
     */
    public function addCurrentStore()
    {
        $this->addFieldToFilter("main_table.store_id", 0);
                    
        $labelExpr = $this->getSelect()->getAdapter()
            ->getIfNullSql('store.value', 'main_table.value');
        $joinCondition = $this->getConnection()
                ->quoteInto(
                    'main_table.attribute_id = store.attribute_id AND store.store_id = ?', 
                    (int)Mage::app()->getStore()->getId());
        $this->getSelect()->joinLeft(
            array('store' => $this->getMainTable()),
            $joinCondition,
            array('store_value' => $labelExpr)
        );
        
        return $this;
    }
}