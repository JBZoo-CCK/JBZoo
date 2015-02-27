/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    var globalAjaxId = 0;

    JBZoo.widget('JBZoo', {}, {

        /**
         * Link to global helper
         */
        jbzoo: window.JBZoo,

        /**
         * Current ajax status
         */
        _isAjax: false,

        /**
         * Ajax auto locker for multi ajax
         */
        _isAjaxLocking: false,

        /**
         * Widget fire on ajax start
         */
        _onAjaxStart: function (options) {
            var $target = (options.target) ? $(options.target) : this.el;
            $target.addClass('jbloading');
        },

        /**
         * Widget fire on ajax end
         */
        _onAjaxStop: function (options, arguments) {
            var $target = (options.target) ? $(options.target) : this.el;
            $target.removeClass('jbloading');
        },

        /**
         * Custom ajax handler
         * @param options = {
         *     'url'     : 'index.php?format=raw&tmpl=component',
         *     'data'    : {},
         *     'dataType': 'json',
         *     'success' : false,
         *     'error'   : false,
         *     'onFatal' : function () {}
         * }
         */
        ajax: function (options) {

            var $this = this;

            globalAjaxId++;

            if ($this._isAjaxLocking && $this._isAjax) {
                JBZoo.logger('i', 'ajax::' + globalAjaxId + ' locked!');
                return $this;
            }

            $this._isAjax = true;
            $.isFunction($this._onAjaxStart) && $this._onAjaxStart.apply($this, [options]);

            JBZoo.logger('w', 'ajax::' + globalAjaxId + ' ->', options);

            var options = $.extend(true, {}, {
                'url'     : 'index.php?format=raw&tmpl=component',
                'data'    : {
                    'rand'  : JBZoo.rand(100, 999), // forced no cache
                    'option': 'com_zoo',
                    'tmpl'  : 'component',
                    'format': 'raw'
                },
                'target'  : false,
                'dataType': 'json',
                'success' : $.noop,
                'error'   : $.noop,
                'onFatal' : function (responce) {
                    if (JBZoo.DEBUG) {
                        JBZoo.logger('e', 'ajax(' + globalAjaxId + ') ->', responce[0].responseText);
                    } else {
                        $this.error('ajax(' + globalAjaxId + ') response no parse');
                    }
                }
            }, options);

            // check url
            if (JBZoo.empty(options.url)) {
                $this._isAjax = false;
                $.isFunction($this._onAjaxStop) && $this._onAjaxStop.apply($this, [options]);
                $this.error("ajax(" + globalAjaxId + ") url is no set!");
                return;
            }

            // jQuery ajax
            $.ajax({
                'url'     : options.url,
                'data'    : options.data,
                'dataType': options.dataType,
                'type'    : 'POST',
                'cache'   : false,
                'headers' : {
                    "cache-control": "no-cache"
                },
                'success' : function (data) {

                    // inner flag & callback
                    $this._isAjax = false;

                    if (typeof data == 'string') {
                        data = $.trim(data);
                    }

                    if (options.dataType == 'json') {
                        JBZoo.logger('i', 'ajax::' + globalAjaxId + ' <-', data);

                        if (data.result && $.isFunction(options.success)) {
                            options.success.apply($this, arguments);

                        } else if (!data.result && $.isFunction(options.error)) {
                            options.error.apply($this, arguments);
                        }

                    } else if ($.isFunction(options.success)) {
                        options.success.apply($this, arguments);
                    }

                    $.isFunction($this._onAjaxStop) && $this._onAjaxStop.apply($this, [options, arguments]);

                },

                'error': function () {
                    // inner flag & callback
                    $this._isAjax = false;
                    $.isFunction($this._onAjaxStop) && $this._onAjaxStop.apply($this, [options, arguments]);

                    options.onFatal(arguments);
                }
            });
        },

        /**
         * Get data from parent or nested element
         * @param key
         * @param selector
         * @returns {*}
         */
        data: function (key, selector) {
            if (selector) {
                return this.$(selector).data(key);
            }
            return this.el.data(key);
        },

        /**
         * Get attr from parent or nested element
         * @param attr
         * @param selector
         * @returns {*}
         */
        attr: function (attr, selector) {
            if (selector) {
                return this.$(selector).attr(attr);
            }
            return this.el.attr(attr);
        },

        /**
         * Plugin fatal error
         * @param message
         */
        error: function (message) {
            return JBZoo.error(this._name + ': ' + message);
        },

        /**
         * Simple system message like alert
         * @param message
         */
        alert: function (message) {
            return JBZoo.alert(message);
        },

        /**
         * Confirm dialogbox
         * @param message
         * @param yesCallback
         * @param noCallback
         */
        confirm: function (message, yesCallback, noCallback) {
            return JBZoo.confirm(message, yesCallback, noCallback, this);
        },

        /**
         * Batch options setting
         * @param options
         * @returns {*}
         */
        setOptions: function (options) {
            var key;
            for (key in options) {
                this._setOption(key, options[key]);
            }
            return this;
        },

        /**
         * Set one option
         * @param key
         * @param value
         * @returns {*}
         */
        setOption: function (key, value) {
            this.options[key] = value;
            return this;
        },

        /**
         * Get option value
         * @param key
         * @returns {*}
         */
        getOption: function (key) {
            return this.options[key];
        },

        /**
         * Get option value
         * @returns {*}
         */
        getOptions: function () {
            return this.options;
        },

        /**
         * @param key
         * @returns String
         */
        _: function (key) {
            return key;
        },

        /**
         * Check and return default value
         * @param value
         * @param defaultValue
         * @returns {*}
         * @private
         */
        _def: function (value, defaultValue) {
            return typeof value !== 'undefined' ? value : defaultValue;
        },

        /**
         * Check widget
         * @param widgetName
         * @returns boolean
         */
        isWidgetExists: function (widgetName) {
            return JBZoo.isWidgetExists(widgetName);
        },

        /**
         * Save var in browser cookie
         * @param key
         * @param value
         * @param namespace
         */
        setCookie: function (key, value, namespace) {

            if (!JBZoo.isWidgetExists('cookie')) {
                return;
            }

            var $this = this,
                ns = $this._def(namespace, $this._name);

            $.cookie(ns + '_' + key, value, {
                'path'   : '/',
                'expires': 365
            });
        },

        /**
         * Get var from browser cookie
         * @param key
         * @param defaultVal
         * @param namespace
         * @returns {*}
         */
        getCookie: function (key, defaultVal, namespace) {

            if (!JBZoo.isWidgetExists('cookie')) {
                return;
            }

            var $this = this,
                ns = $this._def(namespace, $this._name),
                cookieKey = $.cookie(ns + '_' + key);

            return this._def(cookieKey, defaultVal);
        }


    });

})(jQuery, window, document);
