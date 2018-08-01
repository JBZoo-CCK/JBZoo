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

    $.fn.JBCategoryList = function (option) {

        var option = $.extend({}, {
            'appId': 0,
            'catId': 0
        }, option);

        return $(this).each(function () {

            // init vars, links to DOM objects
            var $element = $(this),
                $appSelectWrap = $(".jbapp-list", $element),
                $catSelectWrap = $('.jbcategory-list', $element),
                $appSelect = $appSelectWrap.find('select'),
                $catSelect = $catSelectWrap.find('select'),
                $value = $('.hidden-value', $element);

            // Joomla 3 hack (chosen)
            if (!$.fn.chosen == undefined) {
                $appSelect.chosen({allow_single_deselect: true});
            }

            /**
             * Update value
             */
            function setValue() {
                var appId = $appSelect.val(),
                    catId = $('.app-' + appId + ' select', $element).val();

                if(typeof catId == 'undefined' || catId == null) {
                    catId = -1;
                }
                console.log(catId);
                $value.val(appId + ':' + catId);
            }

            /**
             * Set data from config
             * @param option
             */
            function initValue(option) {
                $('.app-' + option['appId'], $element).show();

                if (option.catId) {
                    $(".jbcategory-list select", $element)
                        .val(option.catId)
                        .trigger("liszt:updated");
                }

                if (option.appId) {
                    $(".jbapp-list select", $element)
                        .val(option.appId)
                        .trigger("liszt:updated");
                }
            }

            /**
             * Change action for application list
             */
            $appSelect.bind('change', function () {

                var appId = $(this).val();
                $catSelectWrap.hide();
                $('.app-' + appId, $element).show();

                setValue();
            });

            /**
             * Change action for category list
             */
            $catSelect.bind('change', function () {
                setValue();
            });

            // init all widget
            initValue(option);

        });
    };
})(jQuery);
