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

    $.fn.JBInputHelper = function (settings) {

        var options = $.extend({}, {
            'base'    : '.ghost',
            'label'   : '.upgrd-label',
            'classes' : {
                'hover' : 'hover',
                'active': 'active',
                'focus' : 'focus'
            },
            'multiple': false,
            'parent'  : '.shipping-default'
        }, settings);

        return $(this).each(function () {

            var $this = $(this),
                $label = $this.prev(options.label);

            if ($this.hasClass('init')) {
                return $this;
            }

            $this.addClass('init');
            $this.on('click', function () {

                $(options.label).remove(options.classes);
                $label.addClass('checked');
            });

            $label.on('mouseenter', function () {

                $label.addClass('hover');
            });

            $label.on('mouseleave', function () {

                $label.removeClass('hover');
            });

        });
    };

})(jQuery, window, document);