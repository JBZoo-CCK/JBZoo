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

    var instanceId       = 0,
        widgetId         = 0,
        widgets          = {},
        parentSelector   = '{element}',
        documentSelector = '{document}',
        closestSelector  = '{closest';

    /**
     * JBZoo widget factory
     */
    $.extend(JBZoo.constructor.prototype, {

        /**
         * Widget creator
         * @param widgetName
         * @param defaults
         * @param methods
         */
        widget: function (widgetName, defaults, methods) {

            var $jbzoo     = this,
                eventList  = {},
                widgetPath = widgetName.split('.'),
                widgetName = widgetName.replace(/\./g, '');

            $.each(methods, function (key, method) {

                if (key.indexOf(' ') > 0) {
                    // collecting events
                    var keyParts = key.match(/^(.*?)\s(.*)/);
                    if (!$jbzoo.empty(keyParts[1]) && $.isFunction(method)) {
                        var trigger = $.trim(keyParts[1]),
                            target  = $.trim(keyParts[2]);

                        eventList[trigger + ' ' + target] = {
                            'trigger': trigger,
                            'target' : target,
                            'action' : method
                        };

                        delete methods[key];
                    }
                }
            });

            if (widgets[widgetName]) {
                $jbzoo.error('Widget \"' + widgetName + '\" already has defined!');
            }

            widgets[widgetName] = function (element, options) {
                var $this   = this;

                // system
                instanceId++;
                $this._id   = instanceId;
                $this._name = widgetName;

                // for widget
                $this.el      = $(element);
                $this.options = $.extend(true, {}, $this._defaults, options);

                var oldId = $this.el.attr('data-widgetid');

                oldId = oldId ? ',' + $this._id : $this._id;
                $this.el.attr('data-widgetid', oldId);

                // init
                $this._initEvents(this._eventList, this);
                $this.init(this);

                // onCreate event
                if ($.isFunction(this.options.onCreate)) {
                    $this.options.onCreate.apply($this);
                }
            };

            // widget extending
            var extendedDefaultList = {
                    onCreate : $.noop,
                    onDestroy: $.noop
                },
                extendedEventList   = {},
                lastParent          = '';

            if ($jbzoo.countProps(widgetPath) > 1) {
                var parentName = '';

                $.each(widgetPath, function (n, name) {
                    parentName += $.trim(name);

                    if (widgets[parentName] && widgetName != parentName) {
                        lastParent = parentName;

                        $jbzoo.classExtend(widgets[widgetName], widgets[parentName]);

                        extendedEventList   = $.extend(true, extendedEventList, widgets[parentName].prototype._eventList);
                        extendedDefaultList = $.extend({}, extendedDefaultList, widgets[parentName].prototype._defaults);

                    } else if (n + 1 != widgetPath.length) {
                        $.error('Widget "' + parentName + '" is undefined!');
                    }
                });
            }

            // merge
            widgetId++;
            $.extend(widgets[widgetName].prototype,
                {
                    /**
                     * jQuery for widget's element
                     */
                    el: false,

                    /**
                     * Ready use options
                     */
                    options: {},

                    /**
                     * Debug history
                     */
                    _logs: [],

                    /**
                     * internal timers
                     */
                    _timers: {},

                    /**
                     * Only for class extending
                     */
                    _eventList: eventList,

                    /**
                     * Add message to log history
                     * @param message
                     */
                    _log: function (message) {
                        this._logs.push(message);
                        return this._logs;
                    },

                    /**
                     * Custom constructor
                     * @param $this
                     * @private
                     */
                    init: $.noop,

                    /**
                     * Custom destructor
                     */
                    _destroy: $.noop,

                    /**
                     * System destructor
                     */
                    destroy: function () {
                        var $this = this;

                        $this._destroy.apply(this, arguments);
                        $this.off('');
                        $this.el.removeData(this._name);
                        if ($.isFunction(this.options.onCreate)) {
                            this.options.onDestroy.apply($this);
                        }
                    },

                    /**
                     * Auto init events
                     * @param eventList
                     * @param $this
                     */
                    _initEvents: function (eventList, $this) {
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
                    $: function (selector) {
                        if (selector == parentSelector) {
                            return this.el;
                        }

                        if (selector.indexOf(documentSelector + ' ') === 0) {
                            selector = selector.replace(documentSelector + ' ', '');
                            return $(selector);
                        }

                        return $(selector, this.el);
                    },

                    /**
                     * Add actions for DOM with delegate
                     * @param eventName
                     * @param selector
                     * @param callback
                     */
                    on: function (eventName, selector, callback) {

                        var $this         = this,
                            eventCallback = function (event) {

                                var args    = arguments,
                                    newArgs = [event, $this];

                                if (args.length > 1) {
                                    var i = 0;
                                    while (args[i]) {
                                        (i > 0) && newArgs.push(args[i]);
                                        i++;
                                    }
                                }

                                return callback.apply(this, newArgs);
                            };

                        if (eventName.indexOf('.') == -1) {
                            eventName = eventName + '.' + $this._name;
                        }

                        if (selector instanceof jQuery) {
                            return $(selector).on(eventName, eventCallback);

                        } else if (selector == parentSelector) {
                            return $(this.el).on(eventName, eventCallback);

                        } else if (selector.indexOf(documentSelector + ' ') == 0) {
                            selector = selector.replace(documentSelector + ' ', '');
                            return $(selector).on(eventName, eventCallback);

                        } else if (selector.indexOf(closestSelector + ' ') == 0) {
                            selector = selector.replace(closestSelector + ' ', '');
                            selector = selector.replace('}', '');

                            return $(this.el).closest(selector).on(eventName, eventCallback);

                        } else {

                            return $(this.el)
                                .on(eventName, selector, eventCallback)
                                .find(selector);
                        }
                    },

                    /**
                     * Disable DOM events
                     * @param eventName
                     * @param selector
                     * @returns {*}
                     */
                    off: function (eventName, selector) {

                        var $this     = this,
                            eventName = eventName + '.' + $this._name;

                        if (!selector || selector == parentSelector) {
                            return $(this.el).off(eventName);

                        } else {
                            return $(this.el).off(eventName, selector);
                        }
                    },

                    /**
                     * Manual event calling
                     * @param trigger
                     * @param selector
                     * @param data
                     * @private
                     */
                    _trigger: function (trigger, selector, data) {

                        if (arguments.length == 1) {
                            data     = [];
                            selector = parentSelector;

                        } else if (arguments.length == 2) {
                            data     = arguments[1];
                            selector = parentSelector;

                        } else {
                            selector = arguments[1] || parentSelector;
                        }

                        if (trigger.indexOf('.') == -1) {
                            trigger = trigger + '.' + this._name;
                        }

                        this.$(selector).trigger(trigger, data);
                    },

                    /**
                     * @param handler
                     * @param delay
                     * @returns {number}
                     * @private
                     */
                    _delay: function (handler, delay, timerName) {

                        var $this                = this;
                        timerName                = timerName || 'default';

                        clearTimeout($this._timers[timerName]);
                        $this._timers[timerName] = setTimeout(function () {
                            return (typeof handler === "string" ? $this[handler] : handler ).apply($this, arguments);
                        }, delay || 0);

                        return $this._timers[timerName];
                    },

                    /**
                     * -->Experimental<-- feature!!!
                     * @param method
                     * @param args
                     * @returns {*}
                     */
                    _parent: function (method, args) {
                        if (widgets[this._parentName]) {
                            return widgets[this._parentName].prototype[method].apply(this, args);
                        }
                    }

                },
                widgets[widgetName].prototype,
                methods,
                {
                    _widgetId  : widgetId,
                    _parentName: lastParent,
                    _defaults  : $.extend(true, {}, extendedDefaultList, defaults),
                    _eventList : $.extend({}, extendedEventList, eventList)
                }
            );

            // plugin initialize (HANDS OFF!!!)
            $.fn[widgetName] = function (options, isComplex) {

                var args        = arguments,
                    method      = (args[0] && typeof args[0] == 'string') ? args[0] : null,
                    returnValue = this;

                if (method && method.indexOf('_') === 0) {
                    $jbzoo.error('Method \"jQuery.' + widgetName + '.' + method + '\" is protected!');
                }

                if (widgetName.toLowerCase() == 'jbzooprice' || widgetName.toLowerCase().indexOf('jbzoopriceelement') === 0) {
                    var $element = $(this);

                    if (widgets[widgetName].prototype[method] && $element.data(widgetName) && method != "init") {

                        methodValue = $element.data(widgetName)[method].apply(
                            $element.data(widgetName),
                            Array.prototype.slice.call(args, 1)
                        );

                        if (methodValue !== $element.data(widgetName) && methodValue !== undefined) {

                            returnValue = methodValue && methodValue.jquery ?
                                returnValue.pushStack(methodValue.get()) :
                                methodValue;

                            return false;
                        }


                    } else if ((!method || $.isPlainObject(method))) {
                        $element.data(widgetName, new widgets[widgetName]($element, options));

                    } else if (method) {
                        $jbzoo.error("Method \"" + method + "\" does not exist on jQuery." + widgetName);
                    }

                }
                else {

                    this.each(function () {
                        var element  = this,
                            $element = $(this);

                        if (widgets[widgetName].prototype[method] && $element.data(widgetName) && method != "init") {

                            methodValue = $element.data(widgetName)[method].apply(
                                $element.data(widgetName),
                                Array.prototype.slice.call(args, 1)
                            );

                            if (methodValue !== $element.data(widgetName) && methodValue !== undefined) {

                                returnValue = methodValue && methodValue.jquery ?
                                    returnValue.pushStack(methodValue.get()) :
                                    methodValue;

                                return false;
                            }


                        } else if ((!method || $.isPlainObject(method)) && (!$.data(element, widgetName))) {
                            $element.data(widgetName, new widgets[widgetName](element, options));

                        } else if (method) {
                            $jbzoo.error("Method \"" + method + "\" does not exist on jQuery." + widgetName);
                        }
                    });
                }
                // chain jQuery functions
                return returnValue;

            };
        },

        /**
         * Prototype class extending
         * @param Child
         * @param Parent
         */
        classExtend: function (Child, Parent) {
            var JBZooObject             = function () {
            };

            $.extend(JBZooObject.prototype, Child.prototype, Parent.prototype);
            Child.prototype             = new JBZooObject;
            Child.prototype.constructor = Child;
            Child.parent                = Parent.prototype
        },

        /**
         * Check is Widget exists
         * @param widgetName
         * @returns {boolean}
         */
        isWidgetExists: function (widgetName) {
            return !JBZoo.empty($.fn[widgetName]) || $.isFunction($[widgetName]);
        }

    });

})(jQuery, window, document);
