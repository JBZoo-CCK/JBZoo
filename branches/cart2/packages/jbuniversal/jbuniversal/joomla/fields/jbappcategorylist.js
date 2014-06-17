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
