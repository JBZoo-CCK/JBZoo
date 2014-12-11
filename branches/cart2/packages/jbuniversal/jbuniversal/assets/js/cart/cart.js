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
        'text_remove_all' : '',
        'text_remove_item': '',
        'url_quantity'    : '',
        'url_delete'      : '',
        'url_clear'       : '',
        'items'           : {}
    }, {
        shipping   : {},
        params     : {},
        quantity   : {},
        changeDelay: 400,

        'change.JBZooQuantity .jsQuantity': function (e, $this, oldValue, newValue) {
            var itemKey = $(this).closest('.jsCartTableRow').data('key');
            $this._change(itemKey, newValue);
        },

        'click .jsDelete': function (e, $this) {

            var $tableRow = $(this).closest('.jsCartTableRow'),
                itemsCount = $this.$('.jsCartTableRow');

            $this.confirm($this.options.text_remove_item, function () {
                $this.ajax({
                    'url'    : $this.options.url_delete,
                    'target' : $tableRow,
                    'data'   : {
                        'key': $tableRow.data('key')
                    },
                    'success': function (data) {

                        if (itemsCount != 1) {
                            $this._updateData(data.prices);
                            $this._reloadModule();
                        } else {
                            window.location.reload();
                        }

                        $tableRow.remove();
                    },
                    'error'  : function (error) {
                        $this.alert(error);
                    }
                });
            });

        },

        'click .jsDeleteAll': function (e, $this) {

            $this.confirm($this.options.text_remove_all, function () {
                $this.ajax({
                    url    : $this.options.url_clear,
                    success: function () {
                        window.location.reload();
                    }
                });
            });
        },

        /**
         * Change quantity for item
         * @param itemKey
         * @param newValue
         * @returns {boolean}
         * @private
         */
        _change: function (itemKey, newValue) {

            var $this = this;

            if (!$this._getItem(itemKey)) {
                return false;
            }

            $this._delay(function () {
                $this.ajax({
                    url    : $this.options.url_quantity,
                    target : '.js' + itemKey,
                    data   : {
                        value: newValue,
                        key  : itemKey
                    },
                    success: function (data) {
                        $this._updateData(data.prices);
                        $this._reloadModule();
                    },
                    error  : function (data) {
                        if (data.message) {
                            $this.alert(data.message);
                        }
                    }
                });
            }, $this.changeDelay);
        },

        /**
         * Get item info
         * @param rowId
         * @returns {*}
         * @private
         */
        _getItem: function (rowId) {
            return this.options.items[rowId];
        },

        /**
         * @private
         */
        _reloadModule: function () {
            if (this.isWidgetExists('JBZooCartModule')) {
                $('.jsJBZooCartModule').JBZooCartModule('reload');
            }
        },

        /**
         * Set new params from responce
         * @param prices
         * @private
         */
        _updateData: function (prices, context) {

            var $this = this;
            context = context || ''

            $.each(prices, function (key, value) {

                if (typeof value == 'object') {
                    $this._updateData(value, key);

                } else {

                    var selector = '.js' + key;
                    if (context) {
                        selector = '.js' + context + ' ' + '.js' + key;
                    }

                    $this.$(selector).html(value);
                }
            });
        }

    });

})(jQuery, window, document);
