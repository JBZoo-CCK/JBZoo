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
        'url_clean'          : '',
        'url_reload'         : '',
        'url_item_remove'    : '',
        'text_delete_confirm': 'Are you sure?',
        'text_empty_confirm' : 'Are you sure?'
    }, {

        'click .jsDelete': function (e, $this) {
            var $item = $(this).closest('.jsCartItem');

            $this.confirm($this.options.text_delete_confirm, function () {
                $this.ajax({
                    url    : $this.options.url_item_remove,
                    data   : {
                        key: $item.data('key')
                    },
                    success: function () {

                        $('.jsJBPrice-' + $item.data('jbprice')).trigger('removeItem');
                        $this.reload();
                    }
                });
            });
        },

        'click .jsEmptyCart': function (e, $this) {

            $this.confirm($this.options.text_empty_confirm, function () {
                $this.ajax({
                    url    : $this.options.url_clean,
                    success: function () {

                        JBZoo.addVar('cartItems', {});
                        $this._trigger('emptyCart');

                        $this.reload();
                    }
                });
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