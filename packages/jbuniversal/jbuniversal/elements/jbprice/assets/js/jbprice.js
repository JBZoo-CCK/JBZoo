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
            'hash'      : ''
        },
        {
            'template'  : '',
            'elements'  : {},
            'cache'     : {},
            '_namespace': 'JBZooPriceElement',

            init: function ($this) {
                this.elements = {};
                this.cache    = {};

                var _key = this.options.itemId + this.options.identifier;
                var elements  = JBZoo.getVar(_key + '.elements', {});
                this.template = JBZoo.getVar(_key + '.template', {});

                $.each(elements, function (_type, params) {

                    var type        = _type.charAt(0).toUpperCase() + _type.substr(1),
                        element     = $('.js' + type, $this.el),
                        plugName    = $this._namespace + type,
                        defaultName = $this._namespace,
                        widget      = {};

                    if (JBZoo.empty(params)) {
                        params = {};
                    }

                    if ($this.jbzoo.isWidgetExists(plugName)) {
                        widget = element[plugName](params).data(plugName);
                    } else {
                        widget = element[defaultName](params).data(defaultName);
                    }

                    $this.elements[_type] = widget;
                });
            },

            'change .jsSimple :input': function (e, $this) {
                $this.rePaint();
            },

            getHash: function () {

                var values = this._getValues();
                return this._buildHash(values);
            },

            getTemplate: function() {
                return this.template;
            },

            rePaint: function () {

                var hash = this.getHash();

                if (JBZoo.empty(this.cache[hash])) {
                    return this.getVariant();
                }

                return this._rePaint(this.cache[hash]);
            },

            getVariant: function () {
                var $this = this;
                this.ajax({
                    'url'    : $this.options.variantUrl,
                    'data'   : {
                        'args': {
                            'template': this.template,
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
                if (!JBZoo.empty(this.elements[identifier])) {
                    var element = this.elements[identifier];
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
                $.each(data, function (_type, data) {
                    var element = $this.elements[_type];
                    if ((!JBZoo.empty(element)) && ($.isFunction(element["rePaint"]))) {
                        element.rePaint(data);
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