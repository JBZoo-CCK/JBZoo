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

    JBZoo.widget('JBZoo.Price',
        {
            // options
            'variantUrl': '',
            'isInCart'  : false,
            'itemId'    : 0,
            'identifier': '',
            'elements'  : {},
            'hash'      : ''
        },
        {
            'elements'  : {},
            'cache'     : {},
            '_namespace': 'JBZooPriceElement',

            init: function ($this) {
                this.elements = {};
                this.cache = {};
                $.each($this.options.elements, function (key, params) {

                    var _class = key.charAt(0).toUpperCase() + key.substr(1),
                        element = $('.js' + _class, $this.el),
                        plugName = $this._namespace + '_' + key,
                        defaultName = $this._namespace + '_default';
                    if (JBZoo.empty(params)) {
                        params = {};
                    }

                    if ($.isFunction(plugName)) {
                        $this.elements[key] = element[plugName](params).data(plugName);

                    } else if ($.fn[plugName]) {
                        $this.elements[key] = element[plugName](params).data(plugName);

                    } else {
                        $this.elements[key] = element[defaultName](params).data(defaultName);
                    }
                });
            },

            'change .jbprice-simple-param input, .jbprice-simple-param select, .jbprice-simple-param textarea': function (e, $this) {
                $this.rePaint();
            },

            rePaint: function () {

                var hash = this.getHash();

                if (JBZoo.empty(this.cache[hash])) {
                    return this.getVariant();
                }

                return this._rePaint(this.cache[hash]);
            },

            getHash: function () {

                var values = this._getValues();
                return this._buildHash(values);
            },

            getVariant: function () {
                var $this = this;
                this.ajax({
                    'url'    : $this.options.variantUrl,
                    'data'   : {
                        'args': {
                            'values'  : $this._getValues()
                        }
                    },
                    'success': function (data) {

                        $this.cache[$this.getHash()] = data;
                        $this._rePaint(data);
                    },
                    'error'  : function (error) {
                        if (error.message) {
                            $this.alert(error.message);
                        }
                    }
                });
            },

            getValue: function () {
                return this._getValues();
            },

            get: function (identifier, defValue) {
                var element = this.elements[identifier];
                if (!JBZoo.empty(element)) {
                    if ($.isFunction(element["getValue"])) {
                        return element.getValue();
                    }
                }

                return defValue;
            },

            _getValues: function () {

                var values = {};

                $('.jsSimple', this.el).each(function () {

                    var $param = $(this);

                    $('input, select, textarea', $param).each(function () {

                        var $field = $(this),
                            id = $param.data('identifier'),
                            value = '';

                        if ($field.attr('type') == 'radio') {
                            if ($field.is(':checked')) {
                                value = $.trim($field.val());
                                if (!JBZoo.empty(value) || value.length > 0) {
                                    values[id] = {'value': value};
                                }
                            }
                        } else {
                            value = $.trim($field.val());
                            if (!JBZoo.empty(value) || value.length > 0) {
                                values[id] = {'value': value};
                            }
                        }
                    });
                });

                return values;
            },

            _rePaint: function (data) {
                var $this = this;
                $.each(data, function (key, data) {

                    if (!JBZoo.empty($this.elements[key])) {

                        var element = $this.elements[key];

                        if ((!JBZoo.empty(element)) && ($.isFunction(element["rePaint"]))) {
                            element.rePaint(data);
                        }
                    }
                });

            },

            _buildHash: function (values) {
                var hash = [];

                for (var key in values) {
                    if (values.hasOwnProperty(key)) {
                        var val = values[key];
                    }
                    hash.push(key + val.value);
                }

                return hash.join('_');
            },

            _updateCache: function (key, data) {

                var neW = {};
                neW[key] = data;

                this.cache[this.getHash()] = neW;
            }
        }
    );

})(jQuery, window, document);