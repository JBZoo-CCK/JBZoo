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

    /**
     * Show/hide joomla field jbdelimiter
     */
    $.fn.JBZooDelimiter = function (options) {

        var options = $.extend({}, {
            'version': '3'
        }, options);

        var $mode = $('#jform_params_mode'),
            $parent = $(this).parents('#attrib-base'),
            group = $(this).attr('data-group');

        if (options.version == 2) {
            $parent = $(this).parents('.adminformlist');
        }

        $(this).each(function () {

            var $this = $(this);

            if (!$this.hasClass('jbdelimiter-init')) {
                $this.addClass('jbdelimiter-init');
            } else {
                return $this;
            }

            var $control = $this.parents('.control-group');

            if (options.version == 2) {
                $control = $this.parent('li');
            }

            $control.attr('data-group', group).addClass('stop');
            $control.nextUntil('.stop').attr('data-group', group);
        });

        var update = function (selected) {
            $parent.children().each(function () {
                group = $(this).attr('data-group');
                if (group != selected && typeof group != 'undefined' && group != 'close') {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        };

        update($mode.val());

        if ($mode.hasClass('initialized')) {
            return $(this);
        } else {
            $mode.addClass('initialized');
        }

        $mode.on('change', function () {
            update($(this).val());
        });
    };
})(jQuery, window, document);