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

    jQuery(function ($) {

        var joomlaVersion = JBZoo.getVar('joomlaVersion', '3');

        // Menu tabs hack
        $('li[data-href-replace]').each(function (n, obj) {
            var $obj = $(obj),
                replace = $(obj).data('href-replace'),
                $link = $obj.children('a'),
                href = $link.attr('href');

            if (replace) {
                $link.attr('href', href.replace(replace, 'controller=item'));
            }
        });

        // init Joomla CP Scripts
        $('#menu').JBZooAdminMenu(JBZoo.getVar('JBAdminItems', {'items': {}}));

        // fix for print version
        $('.jbzoo .uk-grid').closest('html').addClass('jbzoo-print');

        // fix for modules
        $('#module-form').addClass('jbzoo');

        // hack add parent class for admin panel
        $('body').addClass('jbzoo-joomla-' + joomlaVersion);

        if ($("#nav [data-jbzooversion].active").length) {
            $('<span class="version" />')
                .text("JBZoo " + $("#nav [data-jbzooversion].active").data("jbzooversion"))
                .appendTo("#nav div.bar");
        }

        // some plugins
        $.fn.JBZooKeyValue();
        $.fn.JBZooJKeyValue();
        $.fn.JBZooItemOrder();

        // wrapper for all select with chosen
        $('.jbzoo select').JBZooSelect();
    });

})(jQuery, window, document);
