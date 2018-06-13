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
                        //$this.reload();
                        $('.jsJBZooCartModule').JBZooCartModule('reload');
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

                        //$this.reload();
                        $('.jsJBZooCartModule').JBZooCartModule('reload');
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
                success : function (html) {

                    html = '<div>' + html + '</div>';

                    var content = $(html).find('.jsJBZooCartModule').contents();

                    $this.el.empty().prepend(content);

                    if (window.location.href.indexOf('controller=basket') > 0 && $('.jsCartItem', content).length == 0) {
                        //window.location.reload();
                    }
                }
            });
        }
    });

})(jQuery, window, document);