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
 * Product attribute add/edit form hint tab
 *
 * @package Mageinn_Hint
 * @category Mageinn
 * @author Mageinn
 */
class Mageinn_Hint_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Hint 
    extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageinn/hint/form.phtml');
    }
    
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
    
    /**
     * Retrieve stores collection with default store
     *
     * @return Mage_Core_Model_Mysql4_Store_Collection
     */
    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }
    
    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     *
     * @return array
     */
    public function getOptionValues()
    {
        $values = array();
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
            ->setPositionOrder('asc', true)
            ->load();

        foreach ($optionCollection as $option) {
            $value = array();
            $value['id'] = $option->getId();
            //$value['sort_order'] = $option->getSortOrder();
            $value['hints'] = $this->getOptionHintValues($option->getId());
            foreach ($this->getStores() as $store) {
                $storeValues = $this->getStoreOptionValues($store->getId());
                if (isset($storeValues[$option->getId()])) {
                    $value['store'.$store->getId()] = htmlspecialchars($storeValues[$option->getId()]);
                }
                else {
                    $value['store'.$store->getId()] = '';
                }
            }
            $values[] = new Varien_Object($value);
        }

        return $values;
    }
    
    /**
     * Retrieve attribute option values for given store id
     *
     * @param integer $storeId
     * @return array
     */
    public function getStoreOptionValues($storeId)
    {
        $values = array();
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
            ->setStoreFilter($storeId, false)
            ->load();
        foreach ($valuesCollection as $item) {
            $values[$item->getId()] = $item->getValue();
        }
        
        return $values;
    }
    
    /**
     * Retrieve attribute option hints for given option
     *
     * @param integer $optionId
     * @return array
     */
    public function getOptionHintValues($optionId)
    {
        $collection = Mage::getModel('mageinn_hint/option_hint')
                    ->getCollection()
                    ->addFieldToFilter("option_id", $optionId);
        $storeOptionHints = array();
        foreach($collection as $hint) {
            $storeOptionHints[$hint->getStoreId()] = $hint->getValue();
        } 
        
        $values = array();
        foreach ($this->getStores() as $store) {
            $values[$store->getId()] = isset($storeOptionHints[$store->getId()]) ? $storeOptionHints[$store->getId()] : '';
        }
        return $values;
    }
    
    /**
     * Retrieve frontend hints of attribute for each store
     *
     * @return array
     */
    public function getHintValues()
    {
        $attribute = Mage::registry('entity_attribute');
        $collection = Mage::getModel('mageinn_hint/hint')
                    ->getCollection()
                    ->addFieldToFilter("attribute_id", $attribute->getId());
        $storeHints = array();
        foreach($collection as $hint) {
            $storeHints[$hint->getStoreId()] = $hint->getValue();
        } 
        
        $values = array();
        foreach ($this->getStores() as $store) {
            $values[$store->getId()] = isset($storeHints[$store->getId()]) ? $storeHints[$store->getId()] : '';
        }
        return $values;
    }
    
    
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getWysiwygElementHtml($id)
    {
        $html = Mage::getSingleton('core/layout')
                ->createBlock('adminhtml/widget_button', '', array(
                    'label'   => Mage::helper('catalog')->__('Show / Hide Editor'),
                    'type'    => 'button',
                    'class'   => 'scalable show-hide',
                    'onclick' => 'wysiwygToggle(\''.$id.'\')'
                ))->toHtml();
        
        return $html;
    }
}
