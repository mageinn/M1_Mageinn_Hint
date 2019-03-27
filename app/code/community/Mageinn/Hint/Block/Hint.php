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
 * Catalog attribute layer filter hint
 *
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */
class Mageinn_Hint_Block_Hint extends Mage_Core_Block_Template
{
    /**
     * constructor function
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageinn/hint/hint.phtml');
    }
    
    /**
     * Retrieve title hint block html
     *
     * @param int|string $attributeId
     * @return string
     */
    public function getTitleHintHtml($attributeId)
    {
        if(!is_numeric($attributeId) && is_string($attributeId)) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeId);
            $attributeId = $attribute->getId();
        }
        $hint = $this->getTitleHint($attributeId);
        if(!empty($hint)) {
            return $this->setHint($hint)
                    ->setClass("titlehint")
                    ->toHtml();
        } 
        
        return '';
    }
    
    /**
     * Retrieve option hint block html
     *
     * @param string|Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int $optionId
     * @param string $value
     * @return string
     */
    public function getOptionHintHtml($attribute, $optionId = 0, $value = '')
    {
        if(is_string($attribute)) {
            $attr = Mage::getModel('eav/entity_attribute')
                    ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attribute);
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                    ->load($attr->getId());
        }
        
        if ($optionId == 0 && $attribute->usesSource())
        {
            $optionId = $attribute->getSource()->getOptionId($value); 
        }
        
        $optionHints = $this->getOptionHints($attribute->getId());
        if(!empty($optionHints[$optionId])) {
            return $this->setHint($optionHints[$optionId])
                    ->setClass("optionhint")
                    ->toHtml();
        }
        
        return '';
    }
    
    /**
     * Retrieve title hint for current attribute
     *
     * @param string $attributeId
     * @return Mageinn_Hint_Model_Resource_Option_Hint_Collection
     */
    public function getTitleHint($attributeId)
    {
        $hint = $this->getData('title_hint');
        if (is_null($hint)) {
            $hintsCollection = Mage::getModel('mageinn_hint/hint')
                ->getCollection()
                ->addCurrentStore()
                ->addFieldToFilter("main_table.attribute_id", $attributeId)
                ->load();
            
            $hint = $hintsCollection->getFirstItem()->getStoreValue();
            $this->setData('title_hint', $hint);
        }
        return $hint;
    }
   
    
    /**
     * Retrieve option hints collection for option_id
     *
     * @param string $attributeId
     * @return Mageinn_Hint_Model_Resource_Option_Hint_Collection
     */
    public function getOptionHints($attributeId)
    {
        $hints = $this->getData('option_hints');
        if (is_null($hints)) {
            $hintsCollection = Mage::getModel('mageinn_hint/option_hint')
                ->getCollection()
                ->addCurrentStore()
                ->addFieldToFilter("main_table.attr_id", $attributeId)
                ->load();
            
            $hints = array();
            foreach($hintsCollection as $hint) {
                $hints[$hint->getOptionId()] = $hint->getStoreValue();
            }
            $this->setData('option_hints', $hints);
        }
        return $hints;
    }
}