/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

;
(function ($, window, document, undefined) {

    $.fn.Directories = function (options) {

        var args = arguments,
            method = args[0] ? args[0] : null,
            $this = $(this),
            input = $this;

        this.options = $.extend({
            url      : "",
            title    : "Folders",
            mode     : "folder",
            msgDelete: "Delete"
        }, method);

        var finder = $('<div class="finder" />')
            .insertAfter('body')
            .finder(this.options)
            .delegate("a", "click", function (e) {
                finder.find("li").removeClass("selected");
                var li = $(this).parent().addClass("selected");

                if (options.mode != "file" || li.hasClass("file")) {
                    $this.focus().val(li.data("path")).blur()
                }
            });

        var widget = finder.dialog($.extend({
            autoOpen : false,
            resizable: false,
            open     : function () {
                widget.position({
                    of: handle,
                    my: "center top",
                    at: "center bottom"
                })
            }
        }, this.options)).dialog("widget");

        var handle = $('<span title="' + options.title + '" class="' + options.mode + 's" />')
            .insertAfter(input)
            .bind("click", function () {
                finder.dialog(finder.dialog("isOpen") ? "close" : "open")
            }
        );

        $('<span title="' + options.msgDelete + '" class="delete-file" />')
            .insertAfter(handle)
            .bind("click", function () {
                input.val("")
            }
        );

        $("body").bind("click", function (event) {
            if (finder.dialog("isOpen") && !handle.is(event.target) && !widget.find(event.target).length) {
                finder.dialog("close")
            }
        })
    };

})(jQuery, window, document);







