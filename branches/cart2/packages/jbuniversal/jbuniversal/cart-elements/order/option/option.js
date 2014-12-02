/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

(function ($) {

    var Plugin = function () {
    };

    $.extend(Plugin.prototype, {

        name: "ElementSelect",

        options: {element: null, variable: null, url: ""},

        initialize: function (element, options) {
            this.options = $.extend({}, this.options, options);

            var $this = this;
            this.element = element;
            this.list = element.children("ul");
            this.hidden = this.list.find("li.hidden").detach();

            element
                .delegate("div.delete", "click", function () {
                    $(this)
                        .parent("li")
                        .slideUp(400, function () {
                            $(this).remove();
                            $this.orderOptions()
                        })
                })

                .delegate("div.name-input input", "blur", function () {
                    var option = $(this).closest("li");
                    var text = option.find("div.panel input:text");

                    if ($(this).val() != "" && text.val() == "") {
                        var alias = "";

                        $this.getAlias($(this).val(), function (data) {
                            alias = data ? data : "42";
                            text.val(alias);
                            option.find("a.trigger").text(alias)
                        })
                    }
                })

                .delegate("div.panel input:text", "keydown", function (event) {
                    event.stopPropagation();

                    if (event.which == 13) {
                        $this.setOptionValue($(this).closest("li"))
                    }

                    if (event.which == 27) {
                        $this.removeOptionPanel($(this).closest("li"))
                    }
                })

                .delegate("input.accept", "click", function () {
                    $this.setOptionValue($(this).closest("li"))
                })

                .delegate("a.cancel", "click", function () {
                    $this.removeOptionPanel($(this).closest("li"))
                })

                .delegate("a.trigger", "click", function () {
                    $(this)
                        .hide()
                        .closest("li")
                        .find("div.panel")
                        .addClass("active")
                        .find("input:text")
                        .focus();
                })

                .find("div.add").bind("click", function () {
                    $this
                        .hidden.clone()
                        .removeClass("hidden")
                        .appendTo($this.list)
                        .slideDown(200)
                        .effect("highlight", {}, 1e3)
                        .find("input:first")
                        .focus();
                    $this.orderOptions()
                });

            this.list.sortable({
                handle     : "div.sort-handle",
                containment: this.list.parent().parent(),
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
                    $this.orderOptions()
                }
            })
        },

        setOptionValue: function (option) {
            var $this = this;
            var text = option.find("div.panel input:text");
            var alias = text.val();
            if (alias == "") {
                alias = option.find("div.name-input input").val()
            }
            this.getAlias(alias, function (data) {
                alias = data ? data : "42";
                text.val(alias);
                option.find("a.trigger").text(alias);
                $this.removeOptionPanel(option)
            })
        },

        removeOptionPanel: function (option) {
            option.find("div.panel input:text").val(option.find("a.trigger").show().text());
            option.find("div.panel").removeClass("active")
        },

        orderOptions: function () {
            var pattern = /^(\S+\[option\])\[\d+\](\[name\]|\[value\])$/;
            this.list.children("li").each(function (i) {
                $(this).find("input").each(function () {
                    if ($(this).attr("name")) {
                        $(this).attr("name", $(this).attr("name").replace(pattern, "$1[" + i + "]$2"))
                    }
                })
            })
        },

        getAlias: function (name, callback) {
            var $this = this;
            $.getJSON(this.options.url, {name: name}, function (data) {
                callback(data)
            })
        }
    });

    $.fn[Plugin.prototype.name] = function () {
        var args = arguments;
        var method = args[0] ? args[0] : null;

        return this.each(function () {

            var element = $(this);

            if (Plugin.prototype[method] && element.data(Plugin.prototype.name) && method != "initialize") {
                element.data(Plugin.prototype.name)[method].apply(element.data(Plugin.prototype.name), Array.prototype.slice.call(args, 1))

            } else if (!method || $.isPlainObject(method)) {
                var plugin = new Plugin;
                if (Plugin.prototype["initialize"]) {
                    plugin.initialize.apply(plugin, $.merge([element], args))
                }

                element.data(Plugin.prototype.name, plugin)

            } else {
                $.error("Method " + method + " does not exist on jQuery." + Plugin.name)
            }
        })
    }

})(jQuery);