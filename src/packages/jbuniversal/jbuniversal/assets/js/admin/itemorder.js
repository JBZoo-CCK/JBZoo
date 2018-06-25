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
     * Pseudo jQuery plugin for form filed ItemOrder
     * @param options
     * @constructor
     */
    $.fn.JBZooItemOrder = function (options) {
        $('body').on('click', '.jsItemOrder .jsItemOrderAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsItemOrder'),
                $template = $parent.find('.jbzoo-itemorder-row:first').clone(),
                length = $parent.find('.jbzoo-itemorder-row').length;

            $template.find('select option').removeAttr('selected');
            $template.find('input[type=checkbox]').removeAttr('checked');
            $template.find('label').removeAttr('for');

            var html = '<div class="jbzoo-itemorder-row">' + $template.html() + '</div><br>';
            html = html.split(/_jbzoo_[0-9]_/).join('_jbzoo_' + length + '_');

            $addButton.before(html);

            return false;
        });
    };

})(jQuery, window, document);