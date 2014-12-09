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


    JBZoo.widget('JBZoo.CompareTable', {
        dir: 'v'
    }, {

        init: function ($this) {

            if ($this.options.dir == 'v') {
                $this._vertical();
            } else {
                $this._horizontal();
            }
        },

        _vertical: function () {

            this.$('.jbcompare-row').each(function (n, obj) {

                var $row = $(obj),
                    data = undefined,
                    isEqual = true,
                    $cells = $('.jbcompare-cell', $row);

                $cells.each(function (k, cell) {

                    var cellData = $.trim($(cell).text()).toLowerCase();

                    if (JBZoo.empty(data)) {
                        data = cellData;
                    } else {
                        isEqual = data == cellData;
                    }

                    if (!isEqual) {
                        $row.addClass('jbcompare-not-equal');
                    }
                });

            });
        },

        _horizontal: function () {

            var $cols = this.$('.jbcompare-row:first .jbcompare-cell');

            $cols.each(function (n, mainCell) {

                var $mainCell = $(mainCell),
                    elementid = $mainCell.data('elementid'),
                    $cells = $('.jbcompare-cell-' + elementid),
                    data = undefined,
                    isEqual = true;

                $cells.each(function (k, cell) {

                    var $cell = $(cell),
                        cellData = $.trim($cell.text()).toLowerCase();

                    if (data === undefined) {
                        data = cellData;
                    } else {
                        isEqual = data == cellData;
                    }

                    if (!isEqual) {
                        $cells.addClass('jbcompare-not-equal');
                    }
                });

            });
        }

    });

})(jQuery, window, document);