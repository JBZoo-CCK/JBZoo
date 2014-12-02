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

    JBZoo.widget('JBZoo', {}, {

        /**
         * Link to global helper
         */
        jbzoo: window.JBZoo,

        /**
         * Ajax process flag
         */
        isAjax: false,

        _onAjaxStart: $.noop,
        _onAjaxStop : $.noop,

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

            $this.isAjax = true;
            if ($.isFunction($this._onAjaxStart)) {
                $this._onAjaxStart.apply($this, [options]);
            }

            JBZoo.logger('w', 'ajax::request', options);

            var options = $.extend(true, {}, {
                'url'     : 'index.php?format=raw&tmpl=component',
                'data'    : {
                    'rand'  : JBZoo.rand(100, 999), // forced no cache
                    'option': 'com_zoo',
                    'tmpl'  : 'component',
                    'format': 'raw'
                },
                'dataType': 'json',
                'success' : $.noop,
                'error'   : $.noop,
                'onFatal' : function (responce) {
                    if (JBZoo.DEBUG) {
                        JBZoo.logger('e', 'ajax::request - ' + options.url, options.data);
                        JBZoo.dump(responce.responseText, 'Ajax error responce:');
                    }

                    $this.error("Ajax response no parse");
                }
            }, options);

            if (JBZoo.empty(options.url)) {
                $this.error("AJAX url is no set!");
            }

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
                    $this.isAjax = false;
                    if ($.isFunction($this._onAjaxStop)) {
                        $this._onAjaxStop.apply($this, [options]);
                    }

                    if (typeof data == 'string') {
                        data = $.trim(data);
                    }

                    if (options.dataType == 'json') {
                        //JBZoo.logger('i', 'ajax::responce', {'result': data.result, 'message': data.message});

                        if (data.result && $.isFunction(options.success)) {
                            options.success.apply($this, arguments);

                        } else if (!data.result && $.isFunction(options.error)) {
                            options.error.apply($this, arguments);
                        }

                    } else if ($.isFunction(options.success)) {
                        options.success.apply($this, arguments);
                    }

                },

                'error': function () {
                    // inner flag & callback
                    $this.isAjax = false;
                    if ($.isFunction($this._onAjaxStop)) {
                        $this._onAjaxStop.apply($this, [options]);
                    }

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
            return this.error('Plugin "' + this._name + '": ' + message);
        },

        /**
         * Simple system message like alert
         * @param message
         */
        alert: function (message) {
            return alert(message);
        },

        /**
         * Confirm dialogbox
         * @param message
         * @param yesCallback
         * @param noCallback
         */
        confirm: function (message, yesCallback, noCallback) {
            var $this = this;

            if (confirm(message)) {
                $.isFunction(yesCallback)
                {
                    yesCallback($this);
                }

                return true;
            } else {
                $.isFunction(noCallback)
                {
                    noCallback($this);
                }

                return false;
            }
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
         * Check widget
         * @param widgetName
         * @returns boolean
         */
        isWidgetExists: function (widgetName) {
            return JBZoo.isWidgetExists(widgetName);
        }

    });

})(jQuery, window, document);
