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
            'elements'  : {}
        },
        {
            'elements'  : {},
            'cache'     : {},
            '_namespace': 'JBZooPriceElement',

            init: function ($this) {
                this.elements = {};
                this.cache = {};

                $.each($this.options.elements, function (key, params) {

                    var element = $('.jbprice-param' + key.replace('_', '-'), $this.el),
                        plugName = $this._namespace + key,
                        defaultName = $this._namespace + '_default';

                    if (JBZoo.empty(params)) {
                        params = {};
                    }

                    if ($.isFunction(plugName)) {
                        $this.elements[key] = element[plugName](params);

                    } else if ($.fn[plugName]) {
                        $this.elements[key] = element[plugName](params);

                    } else {
                        $this.elements[key] = element[defaultName](params);

                    }
                });
            },

            'change .jbprice-simple-param input, select, textarea': function (e, $this) {
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

                var values = this.getValues();
                return this._buildHash(values);
            },

            getVariant: function () {
                var $this = this;

                this.ajax({
                    'url'    : $this.options.variantUrl,
                    'data'   : {
                        'args': {
                            'values': $this.getValues()
                        }
                    },
                    'success': function (data) {

                        $this.cache[$this.getHash()] = data;
                        $this._rePaint(data);
                    },
                    'error'  : function (error) {

                    }
                });
            },

            _rePaint: function (data) {

                var $this = this;

                $.each(data, function (key, data) {

                    if (!JBZoo.empty($this.elements[key])) {

                        var $object = $this.elements[key],
                            element = $object.data($this._namespace + key);

                        if (JBZoo.empty(element)) {
                            element = $object.data($this._namespace + '_default');
                        }

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

            getValues: function () {

                var values = {};

                $('.jbprice-simple-param', this.el).each(function () {

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
            }
        }
    );

})(jQuery, window, document);