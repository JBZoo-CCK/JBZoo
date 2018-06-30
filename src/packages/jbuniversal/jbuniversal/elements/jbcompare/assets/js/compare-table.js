/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
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