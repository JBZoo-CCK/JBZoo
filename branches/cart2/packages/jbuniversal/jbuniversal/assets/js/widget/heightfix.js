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
     * Height fix plugin
     */
    $.fn.JBZooHeightFix = function () {

        var $this = $(this), maxHeight = 0;

        setTimeout(function () {
            $('.column', $this).each(function (n, obj) {
                var tmpHeight = parseInt($(obj).height(), 10);
                if (maxHeight < tmpHeight) {
                    maxHeight = tmpHeight;
                }
            }).css({height: maxHeight});
        }, 300);
    };

})(jQuery, window, document);