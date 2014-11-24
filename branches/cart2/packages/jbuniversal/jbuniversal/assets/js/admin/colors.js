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
     * jQuery plugin for color element
     * @param options
     */
    $.fn.JBColorElement = function (options) {

        var options = $.extend({}, {
            message : ' already in the list settings',
            theme   : 'bootstrap',
            position: 'bottom'
        }, options);

        onAdded();

        function initMinicolors(element) {

            if ($.isFunction($.fn.minicolors)) {
                var $minicolors = $(element).find('.jbpicker .jbcolor');

                if ($minicolors.hasClass('jbcolor-colors-init')) {
                    return $minicolors;
                }

                $minicolors.minicolors({
                    theme   : options.theme,
                    position: options.position
                });

                $minicolors.addClass('jbcolor-colors-init');

            } else {

                $$('.jbpicker .jbcolor', element).each(function (item) {

                    if (item.hasClass('jbcolor-colors-init') || item.id == '') {
                        return item;
                    }

                    new MooRainbow(item, {
                        id        : item.id,
                        imgPath   : '../media/system/images/mooRainbow/',
                        startColor: [255, 0, 0],
                        onComplete: function (color) {
                            this.element.value = color.hex;
                        }
                    });
                    item.addClass('jbcolor-colors-init');

                });
            }
        }

        function onAdded() {

            $('#element-list, .jsElementList').on('element.added', function (event, element) {
                initMinicolors(element);
            });
        }


        return $(this).each(function () {

            var $this = $(this);

            if ($this.hasClass('added-initialized')) {
                return $this;
            } else {
                $this.addClass('added-initialized');
            }

            initMinicolors($this);
            $('.jsColorAdd', $this).on('click', function () {

                var error = false,
                    $jbname = $('.jbname', $this),
                    $jbcolor = $('.jbcolor', $this),
                    name = $jbname.val(),
                    val = $jbcolor.val(),
                    color = val.toLowerCase(),
                    textVal = $.trim($('.jbcolor-textarea', $this).val()),
                    text = textVal.toLowerCase(),
                    space = text ? '\n' : '';

                if (color && text.indexOf(color) >= 0) {
                    alert(color + options.message);
                }

                if (!name.length) {
                    $jbname.addClass('error').focus();
                    error = true;
                }

                if (!color.length) {
                    $jbcolor.addClass('error').focus();
                    error = true;
                }

                if (error) {
                    return false;
                }

                $('.jbpicker input', $this).removeClass('error');

                $('.jbcolor-textarea', $this).val(text + space + name + color);
                $jbname.focus();
                $jbname.val('');
                $jbcolor.val('');
                $('.minicolors-swatch span', $this).removeAttr('style');

            });

            $('.jbcolor, .jbname', $this).on('keyup', function (event) {
                if (event.keyCode == 13) {
                    $('.jsColorAdd', $this).trigger('click');
                }
            });

        });

    };

})(jQuery, window, document);