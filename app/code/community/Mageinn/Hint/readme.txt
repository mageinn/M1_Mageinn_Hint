Mageinn_Hint
============

http://mageinn.com/product/attribute-hints/

What Is This?
-------------

This Magento extension allows you to improve the usability of your Magento 
store by enriching product attributes and its options as well as product 
options with descriptive tooltips/hints that work beautifully in every browser 
on any device.

Demo:
http://mageinn.com/sandbox/mobile-phones/iphone-6.html
http://mageinn.com/sandbox/mobile-phones.html


How To Install The Module
-------------------------

1. Extract it and simply copy the files over to the Magento core

2. Refresh the cache

3. Enable the module in "System > Configuration -> MAGEINN -> Attribute's Hint"

4. Enable jQuery in "System > Configuration -> MAGEINN -> General"
NOTE: This step is OPTIONAL, enable only if you don't have any other jQuery 
already loaded.

5. Refresh the cache again

Now you can use the module :)

If you find a problem, obsolete or improper code or such, please contact us 
at http://mageinn.com/contactus/


Compatibility With Other Modules
--------------------------------

MageWorx Advanced Product Options Module
----------------------------------------

If you have MageWorx Advanced Product Options module installed, please do the following:

1. Disable "Default Templates" from "System > Configuration -> MAGEINN -> Attribute's Hint -> Use default templates = No"
2. Add <?php echo Mage::helper('mageinn_hint')->getProductOptionHint($_option->getId());?> before </label> to the 
following templates:

catalog-product-view-options-type-date.phtml
catalog-product-view-options-type-default.phtml
catalog-product-view-options-type-file.phtml
catalog-product-view-options-type-select.phtml
catalog-product-view-options-type-text.phtml

located in:
app/design/frontend/themegroup/theme/template/mageworx/customoptions