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

    /**
     * Plugin constructor
     * @param options
     * @returns {*|HTMLElement}
     * @constructor
     */
    $.fn.JBCascadeSelect = function (options) {

        /**
         * Private methods and properties
         * @private
         */
        var $this = $(this),
        _options = {
            'uniqid'  : '',
            'items'   : null,
            'text_all': 'All'
        },
        _selects = {},
        _init = function ($element, groupNum) {

            _selects[groupNum] = $('select', $element);
            _selects[groupNum]
                .change(function () {
                    var $select = $(this),
                        listOrder = parseInt($select.attr('list-order'), 10),
                        value = $select.val(),
                        parentValues = _parentValues(listOrder, groupNum),
                        $selectNext = $('.jbselect-' + (listOrder + 1), $element);

                    _fill($selectNext, value, parentValues, listOrder, false);

                    if ($selectNext.find('option').length > 1) {
                        _enable($selectNext);
                    }

                    $selectNext.trigger('change');
                })
                .each(function (n, obj) {
                    var $select = $(obj),
                        listOrder = parseInt($select.attr('list-order'), 10),
                        value = $select.val(),
                        parentValues = _parentValues(listOrder, groupNum);

                    _disable($select);
                    if (!_checkValue(value)) {
                        _enable($select);
                    }

                    if ($select.find('option').length > 1) {
                        _enable($select);
                    }
                });
        },
        _fill = function ($select, value, parentValues, listOrder, force) {

            var tempList = _options.items;

            _clear($select);

            if (!force) {
                $.each(parentValues, function (n, obj) {

                    if (typeof tempList[obj] != 'undefined') {
                        tempList = tempList[obj];
                    } else if (!_checkValue(obj)) {
                        return false;
                    } else {
                        tempList = {};
                        return false;
                    }
                });
            }

            $.each(tempList, function (n, obj) {
                _addOption($select, n, n);
            });

        },
        _parentValues = function (listOrder, n) {
            var result = {};

            for (var i = 0; i <= listOrder; i++) {
                var val = $(_selects[n].get(i)).val();
                result[i] = val;
            }

            return result;
        },
        _checkValue = function (value) {

            if (typeof value == 'undefined') {
                return false;
            }

            return !$.inArray(value, ['', ' ', '0']);
        },
        _clear = function ($select) {
            $select.empty();
            _disable($select);
            return _addOption($select, '', _options.text_all);
        },
        _disable = function ($select) {
            $select.attr('disabled', 'disabled');
        },
        _enable = function ($select) {
            $select.removeAttr('disabled');
        },
        _addOption = function ($select, key, value) {
            var $option = $('<option>').attr('value', key).html(value);
            return $select.append($option);
        };

        ////// plugin init
        if (!$this.length) {
            return $this;
        }

        _options = $.extend({}, _options, options);

        $('.jbcascadeselect', $this).each(function (n, obj) {
            _init($(obj), n);
        });

        // init new dinamic add selects
        var $parent = $('.repeat-elements', $this);
        $parent.find('p.add').bind('click', function () {

            var newIndex = $parent.find("li.repeatable-element").length - 1,
                $newObj = $this.find('.jbcascadeselect:eq(' + newIndex + ')');

            $('select', $newObj).each(function (n, obj) {
                if (n != 0) {
                    _clear($(obj));
                } else {
                    $(obj).val('');
                }
            });
            _init($newObj, newIndex);
        });

        return $this;
    };
})(jQuery, window, document);