/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */
/* zoo v3.0.10 */
(function (b) {
    var e = function () {
    };
    b.extend(e.prototype, {name:"autosuggest", options:{prefill:"", allowDuplicates:!0, inputName:"term[]", resultsHighlight:!0, addButtonText:"Add"}, initialize:function (a, c) {
        this.options = b.extend({}, this.options, c);
        var d = this;
        this.input = a;
        b.extend(b.expr[":"], {focus:function (a) {
            return a == document.activeElement
        }});
        a.addClass("as-input").wrap('<ul class="as-selections">').wrap('<li class="as-original">').autocomplete(b.extend({select:function (a, b) {
            d.addItem(b.item);
            b.item.value =
                b.item.label = ""
        }}, this.options)).bind("blur",function () {
                d.selections_holder.addClass("blur").find("li.as-selection-item").removeClass("selected")
            }).bind("focus",function () {
                d.selections_holder.removeClass("blur")
            }).bind("keydown", function (c) {
                switch (c.which) {
                    case 8:
                        "" == a.val() && (c.preventDefault(), li = b("li.as-selection-item:last"), li.is(".selected") ? d.removeItem(li) : d.selectItem(li));
                        break;
                    case 9:
                        c.preventDefault(), d.addItem(a.val())
                }
            });
        this.selections_holder = a.closest("ul.as-selections").bind("click",
            function () {
                a.not(":focus") && a.focus()
            });
        this.original = this.selections_holder.find("li.as-original");
        b('<li class="add-tag-button" >').insertAfter(this.original).text(this.options.addButtonText).bind("click", function () {
            b.each(a.val().split(","), function (a, b) {
                d.addItem(b)
            })
        });
        "string" == typeof this.options.prefill ? b.each(this.options.prefill.split(","), function (a, b) {
            d.addItem(b)
        }) : d.addItem(this.options.prefill);
        a.is(":focus") ? a.focus() : a.blur();
        this.selections_holder.delegate("a.as-close", "click",function () {
            d.removeItem(b(this).parent())
        }).delegate("li.as-selection-item",
            "click", function () {
                d.selectItem(this)
            })
    }, addItem                 :function (a) {
        "string" == typeof a && (a = {label:a.trim(), value:a.trim()});
        if ("" != a.value && (this.options.allowDuplicates || !this.itemExists(a))) {
            var c = b('<li class="as-selection-item">').text(a.label).data("item", a).insertBefore(this.original);
            b('<a class="as-close">&times;</a>').appendTo(c);
            b('<input type="hidden" class="as-value">').attr("name", this.options.inputName).val(a.value).appendTo(c)
        }
        this.input.val("")
    }, removeItem              :function (a) {
        a.remove()
    }, itemExists              :function (a) {
        var c =
            !1;
        this.selections_holder.find("li.as-selection-item").each(function () {
            b(this).data("item") && b(this).data("item").value.toLowerCase() == a.value.toLowerCase() && (c = !0)
        });
        return c
    }, selectItem              :function (a) {
        b("li.as-selection-item", this.selections_holder).not(a).removeClass("selected");
        b(a).addClass("selected");
        this.input.not(":focus") && this.input.focus()
    }});
    b.fn[e.prototype.name] = function () {
        var a = arguments, c = a[0] ? a[0] : null;
        return this.each(function () {
            var d = b(this);
            if (e.prototype[c] && d.data(e.prototype.name) &&
                "initialize" != c)d.data(e.prototype.name)[c].apply(d.data(e.prototype.name), Array.prototype.slice.call(a, 1)); else if (!c || b.isPlainObject(c)) {
                var f = new e;
                e.prototype.initialize && f.initialize.apply(f, b.merge([d], a));
                d.data(e.prototype.name, f)
            } else b.error("Method " + c + " does not exist on jQuery." + e.name)
        })
    }
})(jQuery);
