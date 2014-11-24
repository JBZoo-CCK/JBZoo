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
     * Pseudo jQuery plugin for form filed key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooKeyValue = function (options) {
        $('body').on('click', '.jsKeyValue .jsKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsKeyValue'),
                $template = $parent.find('.jbkeyvalue-row:first').clone(),
                length = $parent.find('.jbkeyvalue-row').length;

            $template.find('input').attr('value', '');

            html = '<div class="jbkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            return false;
        });
    };

})(jQuery, window, document);