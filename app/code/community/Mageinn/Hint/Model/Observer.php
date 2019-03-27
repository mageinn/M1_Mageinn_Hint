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
 * Observer
 *
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */
class Mageinn_Hint_Model_Observer
{
    /**
     * Add "Manage Hint" tab to attribute edit screen
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addExtraTab(Varien_Event_Observer $observer) 
    {
        if(Mage::helper('mageinn_hint')->isEnabled()) {
            $block = $observer->getEvent()->getBlock();

            if ( $block instanceof Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs ) {
                $block->addTabAfter('hint', array(
                    'label'     => Mage::helper('catalog')->__('Manage Hints'),
                    'title'     => Mage::helper('catalog')->__('Manage Hints'),
                    'content'   => $block->getLayout()->createBlock('mageinn_hint/adminhtml_catalog_product_attribute_edit_tab_hint')->toHtml(),
                ),'labels');
            }      
        }
    }
    
    /**
     * HTML After 
     * 
     * @param Varien_Event_Observer $observer
     * @return \Mageinn_AdminExtra_Model_Observer
     */
    public function htmlAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('mageinn_hint')->isEnabled()) {
            return $this;
        }

        $block = $observer->getBlock();
        $transport = $observer->getTransport();
        
        if (!isset($block)) {
            return $this;
        }

        if ( $block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options ) {
            $html = $transport->getHtml();
            $html .= $block->getLayout()->createBlock('mageinn_hint/adminhtml_catalog_product_edit_hint')->toHtml();
            $transport->setHtml($html);
        }
    }
    
    /**
     * Save hints
     * 
     * @param Varien_Event_Observer $observer
     */
    public function saveHints(Varien_Event_Observer $observer) 
    {
        if(Mage::helper('mageinn_hint')->isEnabled()) {
            $object = $observer->getEvent()->getAttribute();
            $stores = Mage::app()->getStores(true);
            $attrId = $object->getId();
            
            // Delete all title and option hints for current attribute
            $hintRes = Mage::getResourceModel('mageinn_hint/hint');
            $optHintRes = Mage::getResourceModel('mageinn_hint/option_hint');
            if ($object->getId()) {
                $hintRes->deleteByAttributeId($attrId);
                $optHintRes->deleteByAttributeId($attrId);
            }
            
            // Save Title Hints
            $titleHints = $object->getFrontendHints();
            if (is_array($titleHints)) {
                $defaultTitleHint = "";
                foreach ($stores as $store) {
                    $storeId = (int) $store->getId();
                    
                    // Skip Admin Store
                    if($storeId == 0)
                        continue;
                    
                    if (isset($titleHints[$storeId])
                        && (!empty($titleHints[$storeId])
                        || $titleHints[$storeId] == "0")
                    ) {
                        $defaultTitleHint = $titleHints[$storeId];
                        $hintModel = Mage::getModel('mageinn_hint/hint');
                        $hintModel->setAttributeId($attrId)
                                ->setStoreId($storeId)
                                ->setValue($defaultHint)
                                ->save();
                    }
                }
                
                // Add Admin(Store 0) value, can't be empty
                if (isset($titleHints[0])
                        && (!empty($titleHints[0])
                        || $titleHints[0] == "0")
                    ) {
                    $defaultTitleHint = $titleHints[0];
                }
                if(!empty($defaultTitleHint)) {
                    $hintModel = Mage::getModel('mageinn_hint/hint');
                    $hintModel->setAttributeId($attrId)
                            ->setStoreId(0)
                            ->setValue($defaultTitleHint)
                            ->save();
                }
            }
            
            
            // Save Option Hints
            $option = $object->getOption();
            if(isset($option['hint'])) {
                $hints = $option['hint'];

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attrId)
                    ->load();
                foreach ($optionCollection as $option) {
                    $defaultHint = "";
                    foreach ($stores as $store) {
                        $storeId = (int) $store->getId();

                        // Skip Admin Store
                        if($storeId == 0)
                            continue;

                        $optionId = $option->getId();

                        if (isset($hints[$optionId][$storeId])
                            && (!empty($hints[$optionId][$storeId])
                            || $hints[$optionId][$storeId] == "0")
                        ) {
                            $defaultHint = $hints[$optionId][$storeId];
                            $optHintModel = Mage::getModel('mageinn_hint/option_hint');
                            $optHintModel->setAttrId($attrId)
                                    ->setOptionId($optionId)
                                    ->setStoreId($storeId)
                                    ->setValue($defaultHint)
                                    ->save();
                        }
                    }

                    // Add Admin(Store 0) value, can't be empty
                    if (isset($hints[$optionId][0])
                            && (!empty($hints[$optionId][0])
                            || $hints[$optionId][0] == "0")
                        ) {
                        $defaultHint = $hints[$optionId][0];
                    }
                    if(!empty($defaultHint)) {
                        $optHintModel = Mage::getModel('mageinn_hint/option_hint');
                        $optHintModel->setAttrId($attrId)
                                ->setOptionId($optionId)
                                ->setStoreId(0)
                                ->setValue($defaultHint)
                                ->save();
                    }
                }
            }
        }
    }
}
