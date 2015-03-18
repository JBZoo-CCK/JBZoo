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
    JBZoo.widget('JBZoo.ShippingType.Newpost', {
        type_doors   : 3,
        type_ware    : 4,
        url_locations: '',
        timeout      : 300
    }, {

        init: function ($this) {
            $this.$('select').JBZooSelect('addChosen', {'width': '95%'}); // init chosen
            $this._toggleType();
        },

        'change .jsDeliveryType': function (e, $this) {
            $this._toggleType();
        },

        'change .jsRegion': function (e, $this) {
            $this._loadList($(this).val(), 'cities', $this.$('.jsRecipientCity'));
            $this.$('.jsWarehouse').JBZooSelect('removeOptions');
        },

        'change .jsRecipientCity': function (e, $this) {
            $this._loadList($(this).val(), 'warehouses', $this.$('.jsWarehouse'), function () {
                $this._updatePrice();
            });
        },

        'change .jsWarehouse': function (e, $this) {
            $this._updatePrice();
        },

        'change input[type=text]': function (e, $this) {
            $this._delay('_updatePrice', $this.options.timeout);
        },

        _loadList: function (value, listType, $targetSelect, callback) {
            var $this = this;

            $this.ajax({
                url    : $this.options.url_locations,
                data   : {
                    args: {
                        type  : listType,
                        region: value
                    }
                },
                success: function (data) {
                    $targetSelect.JBZooSelect('newOptions', data.list);
                    $targetSelect.JBZooSelect('val', '');
                    if ($.isFunction(callback)) {
                        callback(arguments);
                    }
                }

            });
        },

        _toggleType: function () {

            var $this = this,
                value = $this.$('.jsDeliveryType').val();

            if (value == $this.options.type_doors) {
                $this.$('.jsDoorsWrapper').show();
                $this.$('.jsWarehouseWrapper').hide();

            } else if (value == $this.options.type_ware) {
                $this.$('.jsDoorsWrapper').hide();
                $this.$('.jsWarehouseWrapper').show();
            }
        }

    });

})(jQuery, window, document);