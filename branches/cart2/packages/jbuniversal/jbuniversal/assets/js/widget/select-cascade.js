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
     * Height fix plugin
     */
    JBZoo.widget('JBZoo.CascadeSelect', {
        'uniqid'  : '',
        'items'   : null,
        'text_all': 'All',
        'group'   : ''
    }, {

        init: function ($this) {
            $this._initZooRepeatable();
            $this._initSelects();
        },

        _initSelects: function () {

            var $this = this,
                $selects = this.$('select');
            $selects.JBZooSelect({});

            $selects.each(function (n, obj) {
                var $select = $(obj),
                    value = $select.JBZooSelect('val');

                $select.JBZooSelect('disable');
                if (!$this._checkValue(value)) {
                    $select.JBZooSelect('enable');
                }

                if ($select.find('option').length > 1) {
                    $select.JBZooSelect('enable');
                }
            });
        },

        /**
         * Init Zoo repeatable feature
         * @private
         */
        _initZooRepeatable: function () {
            var $this = this,
                $parent = $this.el.closest('.jsCascadeGroup');

            if (!$parent.is('.jsCascadeRepeatable')) {

                $parent
                    .addClass('jsCascadeRepeatable')
                    .find('p.add')
                    .bind('click', function () {

                        // init new selects (just added)
                        var $newCascade = $parent.find($this.options.group + ':last');

                        // clean up all selects
                        $('select', $newCascade)
                            .JBZooSelect() // init widget
                            .JBZooSelect('replaceOptions', {'': $this.options.text_all})
                            .JBZooSelect('disable')

                            // load options to first
                            .first()
                            .JBZooSelect('replaceOptions', $this._convertOptions($this.options.items, true))
                            .JBZooSelect('enable');

                        // init cascade
                        $newCascade.JBZooCascadeSelect($this.options);
                    });
            }
        },

        'change select': function (e, $this) {
            var $select = $(this),
                selectIndex = $select.data('rowindex'),
                selectValue = $select.JBZooSelect('val'),
                parentValues = $this._getParentValues(selectIndex),
                $selectNext = $this.$('.jsSelect-' + (selectIndex + 1));

            $this._fillSelect($selectNext, selectValue, parentValues, false);


            if ($selectNext.find('option').length == 2) {
                $selectNext.val($selectNext.find('option:eq(1)').val());
            }

            $selectNext.trigger('change');
        },

        /**
         * Get parent select values
         * @param selectIndex
         * @returns {{}}
         * @private
         */
        _getParentValues: function (selectIndex) {
            var $this = this,
                result = {};

            for (var i = 0; i <= selectIndex; i++) {
                result[i] = $this.$('.jsSelect-' + i).JBZooSelect('val');
            }

            return result;
        },

        /**
         * Add options in select
         * @param $select
         * @param value
         * @param parentValues
         * @param force
         * @private
         */
        _fillSelect: function ($select, value, parentValues, force) {

            var $this = this,
                tempList = $this.options.items;

            if (!force) {
                $.each(parentValues, function (n, obj) {

                    if (typeof tempList[obj] != 'undefined') {
                        tempList = tempList[obj];
                    } else if (!$this._checkValue(obj)) {
                        return false;
                    } else {
                        tempList = {};
                        return false;
                    }
                });
            }

            var newList = $this._convertOptions(tempList, true);
            $select.JBZooSelect('replaceOptions', newList);

            if ($select.find('option').length > 1) {
                $select.JBZooSelect('enable');
            } else {
                $select.JBZooSelect('disable');
            }
        },

        /**
         * Validate select value
         * @param value
         * @returns {boolean}
         * @private
         */
        _checkValue: function (value) {

            if (typeof value == 'undefined') {
                return false;
            }

            return !$.inArray(value, ['', ' ', '0']);
        },

        /**
         * Convert options
         * @param items
         * @param addAll
         * @returns {{}}
         * @private
         */
        _convertOptions: function (items, addAll) {

            var $this = this,
                result = {};

            if ($this._def(addAll, false)) {
                result = {'': $this.options.text_all};
            }

            $.each(items, function (n, obj) {
                result[n] = n;
            });

            return result;
        }

    });

})(jQuery, window, document);