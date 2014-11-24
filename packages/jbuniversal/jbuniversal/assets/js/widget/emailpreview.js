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

    $.fn.JBZooEmailPreview = function (options) {
        var options = $.extend({}, {
            'url': ''
        }, options);

        return $(this).each(function () {

            var $this = $(this),
                init = false;

            if (init) {
                return $this;
            }
            init = true;

            $('.jsEmailTmplPreview', $this).on('click', function () {

                $('#jsOrderList', $this).toggle();

                return false;
            });


            $('#jsOrderList .order-id', $this).on('click', function () {

                var $a = $(this),
                    url = options.url + '&id=' + $a.data('id');
                SqueezeBox.initialize({});
                SqueezeBox.open(url, {
                    handler: 'iframe',
                    size   : {x: 1050, y: 700}
                });

                return false;
            })

        });
    }
})(jQuery, window, document);