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
     * jQuery helper plugin for color element
     * @param options
     */
    $.fn.JBColorHelper = function (options) {

        var options = $.extend({}, {
            'multiple': true,
            'method'  : ''
        }, options);

        return $(this).each(function () {

            var $this = $(this);

            $this.find('input[type=' + options.type + ']:checked').next().addClass('checked');

            if ($this.hasClass('jbcolor-initialized')) {
                return $this;
            } else {
                $this.addClass('jbcolor-initialized');
            }

            $('.jbcolor-input', $this).on('click', function () {
                var $obj = $(this);
                if (!options.multiple) {
                    if ($obj.hasClass('checked')) {
                        $obj
                            .attr('checked', false)
                            .addClass('unchecked')
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $obj.trigger('change');
                    } else {
                        $('.jbcolor-input', $this).removeClass('checked');
                        $('.jbcolor-label', $this).removeClass('checked');
                        $obj
                            .attr('checked', true)
                            .addClass('checked')
                            .removeClass('unchecked')
                            .next()
                            .addClass('checked');
                    }
                } else {

                    if ($obj.hasClass('checked')) {
                        $obj
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $obj.trigger('change');
                    } else {
                        $obj
                            .addClass('checked')
                            .next()
                            .addClass('checked');
                    }

                }
            });

        });
    };

})(jQuery, window, document);