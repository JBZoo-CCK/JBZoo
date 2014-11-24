/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /**
     * JBZoo JBPrice Toggler (deprecated!)
     * @deprecated
     * @param elementId
     * @param itemId
     * @constructor
     */
    $.fn.JBZooPriceToggle = function (elementId, itemId) {
        var $priceObj = $('.jsPrice-' + elementId + '-' + itemId + ', .jsJBPriceAdvance-' + elementId + '-' + itemId);
        $priceObj.removeClass('not-in-cart').addClass('in-cart');
        $('.jsJBZooCartModule').JBZooCartModule().JBZooCartModule('reload');
    };

})(jQuery, window, document);