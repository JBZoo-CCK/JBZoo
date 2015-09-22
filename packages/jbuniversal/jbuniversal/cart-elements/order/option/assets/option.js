/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.ElementSelect', {
        element : null,
        variable: null,
        url     : ""
    }, {
        hidden: null,
        list  : null,

        init: function ($this) {

            $this.list = $this.$(' > ul');
            $this.hidden = $this.$("li.hidden").detach();

            $this.list.sortable({
                handle     : ".sort-handle",
                containment: $this.list.parent().parent(),
                placeholder: "dragging",
                axis       : "y",
                opacity    : 1,
                revert     : 75,
                delay      : 100,
                tolerance  : "pointer",
                zIndex     : 99,

                start: function (event, ui) {
                    ui.placeholder.height(ui.helper.height());
                    $this.list.sortable("refreshPositions")
                },

                stop: function (event, ui) {
                    $this._orderOptions()
                }
            });

        },

        _setOptionValue: function ($option) {
            var $this = this,
                text = $option.find(".panel input:text"),
                alias = text.val();

            if (alias == "") {
                alias = $option.find(".name-input input").val()
            }

            $this._getAlias(alias, function (data) {
                alias = data ? data : "42";
                text.val(alias);
                $option.find("a.trigger").text(alias);
                $this._removeOptionPanel($option)
            })
        },

        _removeOptionPanel: function (option) {
            option.find(".panel input:text").val(option.find("a.trigger").show().text());
            option.find(".panel").removeClass("active");
            this._orderOptions();
        },

        _orderOptions: function () {
            var pattern = /^(\S+\[option\])\[\d+\](\[name\]|\[value\])$/;

            this.list.children("li").each(function (i) {

                $(this).find("input").each(function () {

                    var name = $(this).attr("name");
                    if (name) {
                        name = name.replace(pattern, "$1[" + i + "]$2");
                        name = name.replace(/^tmp/, 'positions');
                        name = name.replace(/-\d*\]\[/, '][');
                        $(this).attr("name", name);
                    }
                });

            })
        },

        _getAlias: function (name, callback) {

            var $this = this;

            $this.ajax({
                url     : $this.options.url,
                dataType: 'html',
                data    : {
                    name: name
                },
                success : function (data) {
                    data = $.parseJSON(data);
                    callback(data);
                }
            });
        },

        'click .delete': function (e, $this) {
            $(this)
                .parent("li")
                .slideUp(400, function () {
                    $(this).remove();
                    $this._orderOptions()
                })
        },

        'blur .name-input input': function (e, $this) {

            var option = $(this).closest("li"),
                text = option.find(".panel input:text");

            if ($(this).val() != "" && text.val() == "") {

                var alias = "";

                $this._getAlias($(this).val(), function (data) {
                    alias = data ? data : "42";
                    text.val(alias);
                    option.find("a.trigger").text(alias);
                    $this._orderOptions();
                })
            }
        },

        'keydown .panel input:text': function (e, $this) {
            event.stopPropagation();

            if (event.which == 13) {
                $this._setOptionValue($(this).closest("li"))
            }

            if (event.which == 27) {
                $this._removeOptionPanel($(this).closest("li"))
            }
        },

        'click input.accept': function (e, $this) {
            $this._setOptionValue($(this).closest("li"))
        },

        'click a.cancel': function (e, $this) {
            $this._removeOptionPanel($(this).closest("li"))
        },

        'click a.trigger': function (e, $this) {
            $(this)
                .hide()
                .closest("li")
                .find(".panel")
                .addClass("active")
                .find("input:text")
                .focus();
        },

        'click .add': function (e, $this) {
            $this
                .hidden.clone()
                .removeClass("hidden")
                .appendTo($this.list)
                .slideDown(200)
                .effect("highlight", {}, 1e3)
                .find("input:first")
                .focus();

            $this._orderOptions()
        }

    });

})(jQuery, window, document);
