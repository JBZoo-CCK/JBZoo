/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

(function (t) {
    var i = function () {
    };
    t.extend(i.prototype, {name: "ElementSelect", options: {element: null, variable: null, url: ""}, initialize: function (i, e) {
        this.options = t.extend({}, this.options, e);
        var n = this;
        this.element = i;
        this.list = i.children("ul");
        this.hidden = this.list.find("li.hidden").detach();
        i.delegate("div.delete", "click",function () {
            t(this).parent("li").slideUp(400, function () {
                t(this).remove();
                n.orderOptions()
            })
        }).delegate("div.name-input input", "blur",function () {
            var i = t(this).closest("li");
            var e = i.find("div.panel input:text");
            if (t(this).val() != "" && e.val() == "") {
                var a = "";
                n.getAlias(t(this).val(), function (t) {
                    a = t ? t : "42";
                    e.val(a);
                    i.find("a.trigger").text(a)
                })
            }
        }).delegate("div.panel input:text", "keydown",function (i) {
            i.stopPropagation();
            if (i.which == 13) {
                n.setOptionValue(t(this).closest("li"))
            }
            if (i.which == 27) {
                n.removeOptionPanel(t(this).closest("li"))
            }
        }).delegate("input.accept", "click",function () {
            n.setOptionValue(t(this).closest("li"))
        }).delegate("a.cancel", "click",function () {
            n.removeOptionPanel(t(this).closest("li"))
        }).delegate("a.trigger", "click",function () {
            t(this).hide().closest("li").find("div.panel").addClass("active").find("input:text").focus()
        }).find("div.add").bind("click", function () {
            n.hidden.clone().removeClass("hidden").appendTo(n.list).slideDown(200).effect("highlight", {}, 1e3).find("input:first").focus();
            n.orderOptions()
        });
        this.list.sortable({handle: "div.sort-handle", containment: this.list.parent().parent(), placeholder: "dragging", axis: "y", opacity: 1, revert: 75, delay: 100, tolerance: "pointer", zIndex: 99, start: function (t, i) {
            i.placeholder.height(i.helper.height());
            n.list.sortable("refreshPositions")
        }, stop                   : function (t, i) {
            n.orderOptions()
        }})
    }, setOptionValue          : function (t) {
        var i = this;
        var e = t.find("div.panel input:text");
        var n = e.val();
        if (n == "") {
            n = t.find("div.name-input input").val()
        }
        this.getAlias(n, function (a) {
            n = a ? a : "42";
            e.val(n);
            t.find("a.trigger").text(n);
            i.removeOptionPanel(t)
        })
    }, removeOptionPanel       : function (t) {
        t.find("div.panel input:text").val(t.find("a.trigger").show().text());
        t.find("div.panel").removeClass("active")
    }, orderOptions            : function () {
        var i = /^(\S+\[option\])\[\d+\](\[name\]|\[value\])$/;
        this.list.children("li").each(function (e) {
            t(this).find("input").each(function () {
                if (t(this).attr("name")) {
                    t(this).attr("name", t(this).attr("name").replace(i, "$1[" + e + "]$2"))
                }
            })
        })
    }, getAlias                : function (i, e) {
        var n = this;
        t.getJSON(this.options.url, {name: i}, function (t) {
            e(t)
        })
    }});
    t.fn[i.prototype.name] = function () {
        var e = arguments;
        var n = e[0] ? e[0] : null;
        return this.each(function () {
            var a = t(this);
            if (i.prototype[n] && a.data(i.prototype.name) && n != "initialize") {
                a.data(i.prototype.name)[n].apply(a.data(i.prototype.name), Array.prototype.slice.call(e, 1))
            } else if (!n || t.isPlainObject(n)) {
                var l = new i;
                if (i.prototype["initialize"]) {
                    l.initialize.apply(l, t.merge([a], e))
                }
                a.data(i.prototype.name, l)
            } else {
                t.error("Method " + n + " does not exist on jQuery." + i.name)
            }
        })
    }
})(jQuery);