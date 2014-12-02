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

    $.fn.JBZooPriceAdvanceBalanceHelper = function () {

        return $(this).each(function () {

            var $this = $(this),
                init = false;

            if (init == true) {
                return $this;
            }

            var $input = $('.jsBalanceInput', $this);

            function change(val) {
                $input.removeAttr('disabled');

                if (val != 1) {
                    $input.attr('disabled', true);
                    $input.val('');
                }
            }

            $('input[type="radio"]', $this).on('change', function () {
                var $radio = $(this);

                change($radio.val());
            });

            change($('input[type="radio"]:checked', $this).val());

            init = true;
        });
    };

})(jQuery, window, document);