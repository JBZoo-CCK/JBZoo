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
     * Pseudo jQuery plugin for form filed joomla key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooJKeyValue = function (options) {

        $('body').on('click', '.jsJKeyValue .jsJKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsJKeyValue'),
                $template = $parent.find('.jbjkeyvalue-row:first').clone(),
                length = $parent.find('.jbjkeyvalue-row').length;


            $template.find('input').attr('value', '');
            $template.find('div').remove();
            $template.find('select').removeClass().show();
            $template.find('select option:selected').removeAttr('selected');

            if (length != 0) {
                $template.append('<a href="#jbjkeyvalue-rem" class="jsJKeyValueRemove">');
            }

            var html = '<div class="jbjkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('0key', (length) + 'key');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            if (typeof jQuery.fn.chosen !== 'undefined') {
                jQuery('.jbjkeyvalue-row:last select').chosen({
                    disable_search_threshold: 10,
                    allow_single_deselect   : true
                });
            }

            return false;
        });

        $('body').on('click', '.jsJKeyValue .jsJKeyValueRemove', function () {
            var $remButton = $(this),
                $row = $remButton.closest('.jbjkeyvalue-row'),
                $parent = $remButton.closest('.jsJKeyValue'),
                $pattern = /[0-9]+?/;

            $row.remove();

            $parent.find('.jbjkeyvalue-row').each(function (key, obj) {
                var $obj = $(obj),
                    $keyName = $('select', $obj).attr('name'),
                    $id = $('select', $obj).attr('id'),
                    $newName = $keyName.replace($pattern, (key)),
                    $newValue = $newName.replace('[key]', '[value]'),
                    $newId = $id.replace($pattern, (key));

                $('div', $obj).remove();
                $('select', $obj).removeClass().show().attr('name', $newName).attr('id', $newId);

                if (typeof jQuery.fn.chosen !== 'undefined') {
                    $('select', $obj).chosen('destroy');

                    jQuery('select', $obj).chosen({
                        disable_search_threshold: 10,
                        allow_single_deselect   : true
                    });
                }

                $('input', $obj).attr('name', $newValue);

            });

            return false;
        });
    }

})(jQuery, window, document);