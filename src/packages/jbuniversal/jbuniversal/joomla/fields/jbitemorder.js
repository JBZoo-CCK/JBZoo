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

(function ($) {

    $.fn.JBItemOrder = function (option) {

        var option = $.extend({}, {
            'order'  : 'priority',
            'reverse': '0',
            'random' : '0'
        }, option);

        return $(this).each(function () {

            // init vars, links to DOM objects
            var $element = $(this),
                $reverse = $(".order-reverse", $element),
                $random = $(".order-random", $element),
                $order = $(".order-select", $element),
                $value = $('.hidden-value', $element);

            // Joomla 3 hack (chosen)
            if (typeof $.fn.chosen != 'undefined') {
                $order.chosen({allow_single_deselect: true});
            }

            /**
             * Update value
             */
            function setValue() {

                if ($random.is(":checked")) {
                    $value.val('random');
                } else {
                    if ($reverse.is(':checked')) {
                        $value.val('r' + $order.val());
                    } else {
                        $value.val($order.val());
                    }
                }
            }

            /**
             * Set data from config
             * @param option
             */
            function initValue(option) {
                $order.val(option['order']).trigger("liszt:updated");
                if (option['reverse'] == '1') {
                    $reverse.attr("checked", "checked");
                }

                if (option['random'] == '1') {
                    $random.attr("checked", "checked");
                }

            }

            $order.bind('change', function () {
                setValue();
            });

            $reverse.bind('change', function () {
                setValue();
            });

            $random.bind('change', function () {
                setValue();
            });

            // init all widget
            initValue(option);
        });
    };
})(jQuery);
