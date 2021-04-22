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
    JBZoo.widget('JBZoo.ShippingType.Boxberry', {
            'api_token'         : '',
            'sum'               : '',
            'city'              : '',
            'free'              : '',
            'weight'            : '',
            'box_lenght'        : '',
            'box_height'        : '',
            'box_width'         : '',
            'yandex_map_key'    : '',
        }, {
    
        init: function ($this) {
            $this.el.data('weight', $this.options.weight);
        },

        clearShipping: function(weight) {
            var $this = this;
            var weight = weight || false;

            if (weight) {
                $this.el.data('weight', weight);

                $this.el.find('.jsBoxberryPzv, .jsBoxberryAddress, .jsBoxberryValue').val('');
                $this.el.find('.jsGetBoxberry').text(JBZoo.getVar('JBZOO_ELEMENT_SHIPPING_BOXBERRY_SELECT', 'Select'));
                $this.$('.boxberry__result').hide();
            }

            $this._delay('_updatePrice', 300);
        },

        'click .jsGetBoxberry': function (e, $this) {
            var token       = $this.options.api_token;
            var sum         = $this.options.sum;
            var city        = $this.options.city;
            var weight      = $this.el.data('weight') || $this.options.weight;
            var length      = $this.options.box_length;
            var height      = $this.options.box_height;
            var width       = $this.options.box_width;
            var mapKey      = $this.options.yandex_map_key;

            if (mapKey) {
                ymaps.geolocation.get({
                    provider: 'yandex',
                    autoReverseGeocode: true
                }).then(function (result) {
                    var metaData    = result.geoObjects.get(0).properties.get('metaDataProperty');
                    var userCity    = metaData.GeocoderMetaData.AddressDetails.Country.AddressLine;

                    boxberry.open(
                        function(result) {
                            $this.setPVZ(result, $this);
                        }, 
                        token,
                        userCity,
                        city,
                        sum,
                        weight, 
                        0,
                        height,
                        width,
                        length
                    );
                });
            } else {
                boxberry.open(
                    function(result) {
                        $this.setPVZ(result, $this);
                    }, 
                    token,
                    '',
                    city,
                    sum,
                    weight, 
                    0,
                    height,
                    width,
                    length
                );
            }

            return false;
        },

        setPVZ: function (result, $this) {
            var res     = $this.el.find('.jsBoxberryResult');
            var pvz     = $this.el.find('.jsBoxberryPzv');
            var address = $this.el.find('.jsBoxberryAddress');
            var value   = $this.el.find('.jsBoxberryValue');
            var button  = $this.el.find('.jsGetBoxberry');

            $this.el.find('.jsBoxberryPzv').val(result.id);
            $this.el.find('.jsBoxberryAddress').val(result.address);
            $this.el.find('.jsBoxberryValue').val(result.price);
            $this.el.find('.jsGetBoxberry').text(JBZoo.getVar('JBZOO_ELEMENT_SHIPPING_BOXBERRY_CHANGE', 'Change'));

            $this.$('.boxberry__result').show();
            $this.$('.jsBoxberryPvzAddress').text('ID: ' + result.id + ', ' + result.address);

            $this._updatePrice();
        },

        'jbzooShippingAjax {closest .jsShippingElement}': function (e, $this) {
            $this.onRecountAjax($(this).data('JBZooShippingAjax'), $this);
        },

        onRecountAjax: function (params, $this) {
            if ($this.el.data('weight') != params.weight) {
                $this.clearShipping(params.weight);
            }
        },
    });

})(jQuery, window, document);