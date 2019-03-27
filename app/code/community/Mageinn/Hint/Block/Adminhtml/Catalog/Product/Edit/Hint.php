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
class Mageinn_Hint_Block_Adminhtml_Catalog_Product_Edit_Hint 
    extends Mage_Adminhtml_Block_Template
{
    protected $_productInstance;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageinn/hint/option.phtml');
    }
    
    /**
     * Get MageWork_CustomOptions data
     * 
     * @return array
     */
    public function getMageWorxTemplateData() {
        if ($data = Mage::getSingleton('adminhtml/session')->getData('customoptions_data')) {
            if (isset($data['general'])) {
                return $data['general'];
            } else {
                return null;
            }
        } elseif (Mage::registry('customoptions_data')) {
            return Mage::registry('customoptions_data')->getData();
        }
    }
    
    /**
     * Get Options Hints
     * 
     * @return string
     */
    public function getOptionHints()
    {
        $optionsArr = array();
        
        // MageWork_CustomOptions compatibility code
        $optionsHash = '';
        $data = $this->getMageWorxTemplateData();
        if (isset($data['hash_options'])) $optionsHash = $data['hash_options'];
        if (!empty($optionsHash)) {
            $options = unserialize($optionsHash);
            foreach($options as $oId => $opt) {
                if(!empty($opt['option_hint'])) {
                    $optionsArr[$oId] = $opt['option_hint'];
                }
            }
        } else {
            // Product options
            $options = $this->getProduct()->getOptions();
            foreach($options as $opt) {
                $hint = Mage::helper('mageinn_hint')->getMageWorxHint($opt);
                if(empty($hint)) {
                    $hint = $opt->getOptionHint();
                }
                if(!is_null($hint)) {
                    $optionsArr[$opt->getId()] = $hint;
                }
            }
        }
        return json_encode($optionsArr);
    }
    
    /**
     * Get Product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = Mage::getSingleton('catalog/product');
            }
        }

        return $this->_productInstance;
    }
}
