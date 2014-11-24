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