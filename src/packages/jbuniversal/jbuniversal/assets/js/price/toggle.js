/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
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