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