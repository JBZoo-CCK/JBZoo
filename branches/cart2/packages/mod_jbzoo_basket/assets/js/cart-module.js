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
     * JBZoo Cart Module
     */
    JBZoo.widget('JBZoo.CartModule', {
        'url_clean'      : '',
        'url_reload'     : '',
        'url_item_remove': ''
    }, {

        'click .jsDelete': function (e, $this) {
            var $item = $(this).closest('.jsCartItem');

            $this.ajax({
                url    : $this.options.url_item_remove,
                data   : {
                    key: $item.data('key')
                },
                success: function () {
                    $this.reload();
                }
            });
        },

        'click .jsEmptyCart': function (e, $this) {

            $this.ajax({
                url    : $this.options.url_clean,
                success: function () {

                    JBZoo.addVar('cartItems', {});
                    $this._trigger('emptyCart');

                    $this.reload();
                }
            });
        },

        /**
         * Full module reload
         */
        reload: function () {
            var $this = this;

            $this.ajax({
                url     : $this.options.url_reload,
                dataType: 'html',
                success : function (data) {
                    $this.el.empty().prepend($(data).contents());
                }
            });
        }
    });

})(jQuery, window, document);