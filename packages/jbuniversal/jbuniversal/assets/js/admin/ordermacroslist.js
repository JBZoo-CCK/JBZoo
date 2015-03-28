/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.OrderMacrosList', {},
        {
            init: function () {
                this.$('.jsMacrosList').hide();
            },

            'click .jsShow': function (e, $this) {
                var $list = $this.$('.jsMacrosList');
                if ($list.is(':hidden')) {
                    $list.slideDown();
                } else {
                    $list.slideUp();
                }

            }
        }
    );

})(jQuery, window, document);