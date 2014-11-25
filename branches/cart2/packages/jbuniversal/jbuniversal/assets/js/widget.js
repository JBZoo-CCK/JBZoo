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

    $.extend(JBZoo.constructor.prototype, {

        /**
         * Prototype class extending
         * @param Child
         * @param Parent
         */
        classExtend: function (Child, Parent) {
            var Func = function () {
            };

            Func.prototype = Parent.prototype;
            Child.prototype = new Func;
            Child.prototype.constructor = Child;
            Child.parent = Parent.prototype
        },

        /**
         * Widget creator
         * @param widgetName
         * @param defaults
         * @param methods
         */
        widget: function (widgetName, defaults, methods) {

            var $jbzoo = this,
                eventList = {};

            $.each(methods, function (key, method) {

                if (key.indexOf(' ') > 0) {
                    // collecting events
                    var keyParts = key.match(/^(.*?)\s(.*)/);
                    if (!$jbzoo.empty(keyParts[1]) && $.isFunction(method)) {
                        var trigger = $.trim(keyParts[1]),
                            target = $.trim(keyParts[2]);

                        eventList[trigger + ' ' + target] = {
                            'trigger': trigger,
                            'target' : target,
                            'action' : method
                        };

                        delete methods[key];
                    }
                }
            });

            /**
             * System constructor
             * @param element
             * @param options
             * @constructor
             */
            function Plugin(element, options) {
                this._name = widgetName.replace('.', '');
                this.el = $(element);
                this.options = $.extend({}, defaults, options);
                this.init(this);
                this.initEvents(this._eventList, this);
            }

            // widget extending
            if (widgetName.indexOf('.') > 0) {
                var widgetPath = widgetName.split('.'),
                    fullName = '';

                $.each(widgetPath, function (n, name) {
                    fullName += name;

                    if ($.fn[fullName]) {
                        var Parent = $.fn[fullName]('getPrototype');
                        JBZoo.classExtend(Plugin, Parent);
                        eventList = $.extend({}, Parent.prototype._eventList, eventList);

                    } else if (n + 1 != widgetPath.length) {
                        $jbzoo.error('Widget "' + fullName + '" is undefined!');

                    } else {
                        widgetName = fullName;
                    }
                });
            }

            // merge
            $.extend(Plugin.prototype, {

                /**
                 * jQuery for widget's element
                 */
                'el': false,

                /**
                 * Ready use options
                 */
                'options': {},

                /**
                 * Debug history
                 */
                '_logs': [],

                /**
                 * Only for class extending
                 */
                '_eventList': eventList,

                /**
                 * Add message to log history
                 * @param message
                 */
                '_log': function (message) {
                    this._logs.push(message);
                },

                /**
                 * Real widget constructor
                 * @param $this
                 * @private
                 */
                'init': $.noop,

                /**
                 * Auto init events
                 * @param eventList
                 * @param $this
                 */
                'initEvents': function (eventList, $this) {
                    if (!$jbzoo.empty(eventList)) {
                        $.each(eventList, function (n, eventData) {
                            $this.on(eventData.trigger, eventData.target, eventData.action);
                        });
                    }
                },

                /**
                 * For easy selecting with widget context
                 * @param selector
                 * @returns jQuery
                 */
                '$': function (selector) {
                    return $(selector, this.el);
                },

                /**
                 * Add actions for DOM with delegate
                 * @param eventName
                 * @param selector
                 * @param callback
                 */
                'on': function (eventName, selector, callback) {
                    var $this = this;

                    if (selector == '{element}') {
                        return $(this.el).on(eventName, function (event) {
                            return callback.apply(this, [event, $this]);
                        });
                    } else {
                        return $(this.el)
                            .on(eventName, selector, function (event) {
                                return callback.apply(this, [event, $this]);
                            })
                            .find(selector);
                    }
                },

                /**
                 * -->Experimental<-- feature!!!
                 * @param method
                 * @param args
                 * @returns {*}
                 */
                '_parent': function (method, args) {
                    if (Plugin.constructor.parent) {
                        return Plugin.constructor.parent[method].apply(this, args);
                    }
                }

            }, methods);

            // plugin initialize (HANDS OFF!!!)
            $.fn[widgetName] = function (options) {
                var args = arguments,
                    method = (args[0] && typeof args[0] == 'string') ? args[0] : null;

                if (method == 'getPrototype') {
                    return Plugin;
                }

                this.each(function () {
                    var element = this,
                        $element = $(this);

                    if (Plugin.prototype[method] && $element.data(widgetName) && method != "init") {

                        $element.data(widgetName)[method].apply(
                            $element.data(widgetName),
                            Array.prototype.slice.call(args, 1)
                        );

                    } else if ((!method || $.isPlainObject(method)) && (!$.data(element, widgetName))) {
                        $element.data(widgetName, new Plugin(element, options));

                    } else if (method) {
                        $jbzoo.error("Method \"" + method + "\" does not exist on jQuery." + widgetName);
                    }
                });

                // chain jQuery functions
                return $(this);
            };
        }
    });

})(jQuery, window, document);
