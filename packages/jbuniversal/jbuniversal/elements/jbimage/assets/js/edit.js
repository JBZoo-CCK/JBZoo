/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */


jQuery(function ($) {

    var url = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

    var initJbImage = function (i, isCopy) {

        var $element = $(this);

        if ($element.find('image-preview').length) {
            return;
        }

        var parentClass   = $element.closest('.repeat-elements').attr('id'),
            id            = parentClass + '-jbimage-' + i,
            $selectButton = $('<button type="button" />').text("Select Image").insertAfter($element),
            $cancelSelect = $("<span />").addClass("image-cancel").insertAfter($element),
            $imagePreview = $("<div />").addClass("image-preview").insertAfter($selectButton);

        $element.attr("id", id);
        $element.val() && $("<img />").attr("src", url + $element.val()).appendTo($imagePreview);

        $cancelSelect.click(function () {
            $element.val("");
            $imagePreview.empty();
        });

        $selectButton.click(function (event) {
            event.preventDefault();

            SqueezeBox.fromElement(this, {
                handler: "iframe",
                url    : "index.php?option=com_media&view=images&tmpl=component&e_name=" + id,
                size   : {x: 850, y: 500}
            });
        });

        if (isCopy) {
            $cancelSelect.trigger('click');
        }

    };

    $("input.jbimage-select").each(function (n, obj) {
        initJbImage.apply(obj, [n, false]);
        var $parent = $(obj).closest('.element'),
            $addButton = $parent.find('p.add');

        if (!$addButton.data('jbimage-init')) {

            $addButton.data('jbimage-init', true);

            $addButton.bind('click', function () {
                var newIndex = $parent.find("li.repeatable-element").length + 1,
                    $element = $parent.find('input.jbimage-select:last');

                initJbImage.apply($element, [newIndex, true]);
            });
        }
    });

    if ($.isFunction(window.jInsertEditorText)) {
        window.insertTextOldJBImage = window.jInsertEditorText;
    }

    window.jInsertEditorText = function (c, a) {

        if (a.match(/-jbimage-/)) {

            var $element = $("#" + a),
                value    = c.match(/src="([^\"]*)"/)[1];

            $element.parent()
                .find("div.image-preview")
                .html(c)
                .find("img")
                .attr("src", url + value);

            $element.val(value);

        } else {
            $.isFunction(window.insertTextOldJBImage) && window.insertTextOldJBImage(c, a);
        }
    };

});
