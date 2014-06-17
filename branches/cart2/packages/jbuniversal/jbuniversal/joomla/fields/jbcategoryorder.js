/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */


(function ($) {

    $.fn.JBCategoryOrder = function (option) {

        var option = $.extend({}, {
            'order'  : 'ordering',
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
                if (option.order) {
                    $order.val(option.order).trigger("liszt:updated");
                }

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