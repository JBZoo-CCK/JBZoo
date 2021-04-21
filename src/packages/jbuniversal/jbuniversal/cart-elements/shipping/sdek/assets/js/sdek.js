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

    var active;

    JBZoo.widget('JBZoo.ShippingType.Sdek', {
            'path'          : '',
            'servicePath'   : '',
            'templatePath'  : '',
            'city'          : '',
            'key'           : '',
            'free'          : '',
            'rate'          : 1,
            'goods'         : []
        }, {
    
        init: function ($this) {
            $this.el.data('goods', $this.options.goods);
            $this.initSDEK();
        },

        'click .jsGetSdek': function (e, $this) {
            $this.$('#sdek-map, #sdek-pvz').show();
            
            $this.initSDEK();

            // sdek.open();

            return false;
        },

        clearShipping: function(goods) {
            var $this = this;
            var goods = goods || false;

            if (goods) {
                $this.el.data('goods', goods);

                $this.$('.jsSdekValue, .jsSdekTariff, .jsSdekCityToName, .jsSdekCityToId, .jsSdekAddress, .jsSdekResultCity, .jsSdekPvz').val('');
                $this.$('.jsSdekPvzAddress').text('');
                $this.$('.jsGetSdek').text(JBZoo.getVar('JBZOO_ELEMENT_SHIPPING_SDEK_SELECT', 'Change'));
                $this.$('.sdek__result-courier, .sdek__result-pvz, .sdek__result-city, #sdek-pvz').hide();
            }
            
            $this._delay('_updatePrice', 300);
        },

        initSDEK: function(goods) {
            var $this = this;
            var goods = $this.el.data('goods') || $this.options.goods;

            this.pvz = false;

            var city    = $this.options.city;
            var cityArr = city.split(',');
            var city    = cityArr[0];

            var sdek = new ISDEKWidjet ({
                cityFrom    : city,
                defaultCity : 'auto',
                path        : $this.options.path,
                servicepath : $this.options.servicePath,
                templatepath: $this.options.templatePath,
                apikey      : $this.options.key,
                rate        : $this.options.rate,
                goods       : $.parseJSON(goods),
                link        : 'sdek-pvz',
                popup       : true,
                onChoose : function(info) {
                    var pvz = 'ID: ' + info.id + ', ' + info.PVZ.Address;

                    $this.$('.jsSdekTariff').val(info.tarif);
                    $this.$('.jsSdekPvz').val(info.id);
                    $this.$('.jsSdekCityToName').val(info.cityName);
                    $this.$('.jsSdekCityToId').val(info.city);
                    $this.$('.jsSdekAddress').val(pvz);
                    $this.$('.jsSdekValue').val(info.price);
                    $this.$('.jsSdekResultCity').text(info.cityName);
                    $this.$('.jsGetSdek').text(JBZoo.getVar('JBZOO_ELEMENT_SHIPPING_SDEK_CHANGE', 'Change'));

                    $this.$('.sdek__result-pvz, .sdek__result-city').show();
                    $this.$('.sdek__result-courier').hide();
                    $this.$('.jsSdekPvzAddress').text(pvz);

                    $this.$('#sdek-map, #sdek-pvz').hide();

                    $this._updatePrice();
                },
                onChooseProfile : function(info) {
                    $this.$('.jsSdekAddress').val('');
                    $this.$('.jsSdekPvz').val('');
                    $this.$('.jsSdekTariff').val(info.tarif);
                    $this.$('.jsSdekCityToName').val(info.cityName);
                    $this.$('.jsSdekCityToId').val(info.city);
                    $this.$('.jsSdekValue').val(info.price);
                    $this.$('.jsSdekResultCity').text(info.cityName);
                    $this.$('.jsGetSdek').text(JBZoo.getVar('JBZOO_ELEMENT_SHIPPING_SDEK_CHANGE', 'Change'));

                    $this.$('.sdek__result-pvz').hide();
                    $this.$('.sdek__result-courier, .sdek__result-city').show();

                    $this.$('#sdek-map, #sdek-pvz').hide();

                    $this._updatePrice();
                }
            });

            return sdek;
        },

        'keyup .jsSdekAddress': function (e, $this) {
            $this._delay(function () {
                $this._updatePrice();
            }, 1000, 'sdekAddress');
        },

        'focusout .jsSdekAddress': function (e, $this) {
            $this._delay(function () {
                $this._updatePrice();
            }, 50, 'sdekAddress');
        },

        'jbzooShippingAjax {closest .jsShippingElement}': function (e, $this) {
            $this.onRecountAjax($(this).data('JBZooShippingAjax'), $this);
        },

        onRecountAjax: function (params, $this) {
            if ($this.el.data('goods') != params.goods) {
                $this.clearShipping(params.goods);
            }
        },
    });

})(jQuery, window, document);