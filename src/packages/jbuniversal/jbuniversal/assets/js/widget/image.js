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

    $.fn.initJBPriceAdvImage = function () {
        var url = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

        var $form = $('form.item-edit');

        return $('.jbprice-img-row-file', $form).each(function (n) {

            var $this = $(this);

            if ($this.hasClass('JBPriceImage-init')) {
                return $this;
            }

            $this.addClass('JBPriceImage-init');
            var $jsJBPriceImage = $('.jsJBPriceImage', $this),
                id = "jsJBPriceImage-" + n,
                $selectButton = $('<button type="button" class="jbprice-img-button" />').text("Select Image").insertAfter($jsJBPriceImage),
                $cancelSelect = $('<span class="jbprice-img-cancel image-cancel"/>').insertAfter($jsJBPriceImage);

            $jsJBPriceImage.attr("id", id);

            $cancelSelect.click(function () {
                $cancelSelect.prev().val("");
            });

            $selectButton.click(function (event) {
                event.preventDefault();

                SqueezeBox.fromElement(this, {
                    handler: "iframe",
                    url    : "index.php?option=com_media&view=images&tmpl=component&e_name=" + id,
                    size   : {x: 850, y: 500}
                });
            });
            var func = 'insertJBPriceImage' + id;
            if ($.isFunction(window.jInsertEditorText)) {
                window[func] = window.jInsertEditorText;
            }

            window.jInsertEditorText = function (c, a) {

                if (a.match(/^jsJBPriceImage-/)) {

                    var $element = $("#" + a),
                        value = c.match(/src="([^\"]*)"/)[1];

                    $element.parent()
                        .find("img")
                        .attr("src", url + value);

                    $element.val(value);

                } else {
                    $.isFunction(window[func]) &&
                    window[func](c, a);
                }

            };

        })

    };

})(jQuery, window, document);
