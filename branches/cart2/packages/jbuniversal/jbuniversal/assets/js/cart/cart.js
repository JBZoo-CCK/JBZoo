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
     * JBZoo Cart widget
     */
    JBZoo.widget('JBZoo.Cart', {
        'confirm_message': '',
        'url_quantity'   : '',
        'url_delete'     : '',
        'url_clean'      : '',
        'params'         : {}
    }, {
        'shipping': {},
        'params'  : {},

        init: function () {

            this.params = JSON.parse(this.options.params);
            this.$('.jsQuantity').JBZooQuantity(this.params._quantity);
            //$this.module = $('.jsJBZooCartModule').JBZooCartModule();

        },

        change: function (value, row) {
            this.ajax({
                'url'    : this.options.url_quantity,
                'data'   : {
                    'value': value,
                    'key'  : row.key
                },
                'success': function (data) {
                    console.log(data);
                    //$.fn.JBZooPriceReloadBasket();
                },
                'error'  : function (data) {
                    if (data.message) {
                        alert(data.message);
                    }
                }
            });
        },

        'change .jsQuantity': function (e, $this) {

            var jsQuantity = $(this),
                row = $this.rowData(jsQuantity);

            $this.change(jsQuantity.val(), row);
        },

        'keyUp .jsQuantity': function (e, $this) {

            var jsQuantity = $(this),
                row = $this.rowData(jsQuantity);

            $this.change(jsQuantity.val(), row);
        },

        'click .jsDelete': function (e, $this) {

            var jsDelete = $(this),
                row = $this.rowData(jsDelete);
            $this.ajax({
                'url'    : $this.options.url_delete,
                'data'   : {
                    'item_id': row.item_id,
                    'key'    : row.key
                },
                'success': function (data) {
                    row.tr.slideUp(300, function () {
                        row.tr.remove();
                        if (!JBZoo.empty($this.$('tbody tr'))) {
                            window.location.reload();
                        }
                    });

                    //$.fn.JBZooPriceReloadBasket();
                },
                'error'  : function (error) {
                    alert(error);
                }
            });
        },

        'click .jsDeleteAll': function (e, $this) {

            if (confirm($this.options.confirm_message)) {
                JBZoo.ajax({
                    'url'    : $this.options.url_clean,
                    'success': function () {
                        window.location.reload();
                    }
                });
            }
        },

        rowData: function (field) {
            var tr = field.closest('.jbbasket-item-row');
            return {
                'tr'     : $(tr),
                'item_id': tr.data('item_id'),
                'key'    : tr.data('key')
            };
        }

    });

})(jQuery, window, document);