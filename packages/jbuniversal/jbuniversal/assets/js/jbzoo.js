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
         * Current ajax status
         */
        _isAjax: false,

        /**
         * Ajax auto locker for multi ajax
         */
        _isAjaxLocking: true,

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

            if ($this._isAjaxLocking && $this._isAjax) {
                JBZoo.logger('i', 'ajax::request::has locked - ' + options.url, options.data);
                return $this;
            }

            $this._isAjax = true;
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
                'target'  : false,
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
                return;
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
                    $this._isAjax = false;

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

                    if ($.isFunction($this._onAjaxStop)) {
                        $this._onAjaxStop.apply($this, [options, arguments]);
                    }

                },

                'error': function () {
                    // inner flag & callback
                    $this._isAjax = false;
                    if ($.isFunction($this._onAjaxStop)) {
                        $this._onAjaxStop.apply($this, [options, arguments]);
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
            return JBZoo.error('Plugin "' + this._name + '": ' + message);
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

            noCallback = noCallback || $.noop;
            yesCallback = yesCallback || $.noop;

            if (confirm(message)) {
                if ($.isFunction(yesCallback)) {
                    yesCallback.apply($this);
                }

                return true;
            } else {
                if ($.isFunction(noCallback)) {
                    noCallback.apply($this);
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
