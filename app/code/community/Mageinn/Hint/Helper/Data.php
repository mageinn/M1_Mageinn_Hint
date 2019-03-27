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
 * This class is required for common functions getting used around this module.
 * 
 * @author Mageinn
 * @package Mageinn_Hint
 * @category Mageinn
 */
class Mageinn_Hint_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_product;
    protected $_productOptions = array();
    
    protected $_mageWorkOptionHint = array();
    
    const XML_PATH_MAGEINN_HINT_ENABLED = 'mageinn_hint/general/enabled';
    const XML_PATH_MAGEINN_FILTER_TPL   = 'mageinn_hint/general/filter_template';

    /**
     * Checks if the module is enabled
     * 
     * @return bool 
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_MAGEINN_HINT_ENABLED);
    }
    
    /**
     * Get filter template
     * 
     * @return string 
     */
    public function getFilterTemplate() 
    {
        return Mage::getStoreConfig(self::XML_PATH_MAGEINN_FILTER_TPL);
    }
    
    /**
     * Get MageWork_CustomOptions hint
     * 
     * @param type $option
     * @return mixed
     */
    public function getMageWorxHint($option)
    {
        $product = $this->getProduct();
        $key = $product->getId() . '_' . $option->getInGroupId();
        
        if(!isset($this->_mageWorkOptionHint[$key])) {
            $this->_mageWorkOptionHint[$key] = '';
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            try {
                $table1 = $resource->getTableName('mageworx_customoptions/group');
                $table2 = $resource->getTableName('mageworx_customoptions/relation');
            } catch (Exception $e) {
                return false;
            }   
            
            if(empty($table1) || empty($table2)) {
                return false;
            }

            $query = 'SELECT g.group_id, g.hash_options FROM `' . $table1 . '` g, `' 
                    . $table2 . '` r WHERE g.group_id = r.group_id AND '
                    . 'r.product_id = ' . $product->getId() 
                    . ' AND r.option_id = ' . $option->getId();

            $results = $readConnection->fetchRow($query);
            if (!empty($results['hash_options'])) {
                $options = unserialize($results['hash_options']);
                foreach($options as $opt) {
                    $inGroupId = ((intval($opt['in_group_id'])>0)?($results['group_id'] * 65535) + intval($opt['in_group_id']):0);
                    
                    $_key = $product->getId() . '_' . $inGroupId;
                    $this->_mageWorkOptionHint[$_key] = $opt['option_hint'];
                }
            }
        }
        
        return $this->_mageWorkOptionHint[$key];
    }
    
    /**
     * 
     * @return string
     */
    public function getProductOptionHint($optionId)
    {
        $optionHints = $this->getProductOptions();
        $hintBlock = Mage::app()->getLayout()->createBlock('mageinn_hint/hint');
        return $hintBlock->setHint($optionHints[$optionId])->setClass("titlehint")->toHtml();
    }
    
    /**
     * Retrieve product options
     *
     * @return array
     */
    public function getProductOptions()
    {
        $product = $this->getProduct();
                
        if (!isset($this->_productOptions[$product->getId()])) {
            $options = $product->getOptions();
            foreach($options as $opt) {
                $hint = $this->getMageWorxHint($opt);
                if(empty($hint)) {
                    $hint = $opt->getOptionHint();
                }
                
                $this->_productOptions[$product->getId()][$opt->getId()] = $hint;
            }
        }
        return $this->_productOptions[$product->getId()];
    }
    
    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }
    
    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}