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


jQuery(document).ready(function ($) {

    $.fn.JBImageSubmission = function (initOptions) {

        var $this = $(this),
            options = $.extend({}, initOptions);

        function reInit($obj, $selectImage) {
            if ($selectImage.val()) {
                $obj.find(".image-select").addClass("hidden");
                $obj.find(".image-preview").removeClass("hidden");
            } else {
                $obj.find(".image-select").removeClass("hidden");
                $obj.find(".image-preview").addClass("hidden");
            }
        }

        return $this.each(function (n, obj) {

            var $obj = $(obj),
                $advanced = $obj.find("select.image"),
                $fileSelect = $obj.find(".file-select"),
                $selectImage = $advanced.length ? $advanced : $obj.find("input.image"),
                $cancel = $obj.find(".image-cancel");

            // cancel
            $cancel.unbind().bind("click", function () {
                $selectImage.val("");
                reInit($obj, $selectImage);
            });

            // set selected image
            if ($advanced.length) {

                $selectImage
                    .unbind()
                    .bind("change", function () {
                        $obj.find("img").attr("src", JBZoo.getVar('rootUrl') + $selectImage.val());
                        reInit($obj, $selectImage);
                    });
            }

            // set new image on select
            $fileSelect.change(function () {
                var value = this.value.replace(/^.*[\/\\]/g, '');
                $obj.find('.filename').val(value);
            });

            reInit($obj, $selectImage);
        });

    };

    var $jbimages = $('.jbzoo .jbimage-submission');

    $jbimages.JBImageSubmission();

    $jbimages.each(function (n, obj) {

        var $obj = $(obj),
            $parent = $obj.closest('.repeat-elements');

        $parent.find('p.add').bind('click', function () {

            var $elementRow = $parent.find('.jbimage-submission:last');
            $elementRow.JBImageSubmission();
            $elementRow.find('.image-cancel').click();
            $elementRow.find('input').val('');
        });
    });

});
