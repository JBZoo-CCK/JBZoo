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

    JBZoo.widget('JBZoo.EditPositions', {
        'urlAddElement'    : "index.php?option=com_zoo",
        'textNoElements'   : "No elements",
        'textElementRemove': "Are you sure want to delete the element?",
        'isElementTmpl'    : false
    }, {

        _isAjaxLocking: false,

        editableLists: {},
        newElelements: {},

        init: function ($this) {
            $this.editableLists = $this.$(".jsElementList:not(.unassigned)");
            $this.newElelements = $(".jsElement", $(".jsElementList.unassigned"));

            $this._initDragable();
            $this._initSortable();

            $this._emptyList();
            $this._rebuildList();
        },

        _initSortable: function () {

            var $this = this;

            $this.editableLists.each(function (n, list) {
                var $list = $(list);

                $list.sortable({
                    forcePlaceholderSize: true,

                    connectWith: ".jsElementList",
                    placeholder: "jsElement",
                    handle     : ".jsSort",
                    cursorAt   : {top: 16},
                    tolerance  : "pointer",
                    scroll     : false,

                    change: function () {
                        $this._emptyList();
                    },

                    update: function (event, ui) {

                        if (ui.item.hasClass("jsAssigning")) {

                            $this.el.find(".jsAssigning").each(function () {

                                if ($(this).data("config")) {

                                    var $newElem = $(this).data("config").clone();

                                    $newElem.find("input:radio").each(function () {
                                        var newAttrs = $(this).attr("name").replace(/^elements\[[\w_-]+\]/, "elements[_tmp]");

                                        $(this).attr("name", newAttrs);
                                    });

                                    ui.item.prepend($newElem);
                                }
                            });

                            ui.item.removeClass("jsAssigning");
                        }

                        $this._emptyList();
                    },

                    start: function (e, ui) {
                        ui.helper.addClass("ghost")
                    },

                    stop: function (e, ui) {
                        ui.item.removeClass("ghost");
                        $this._emptyList();
                        $this._rebuildList();
                    }
                });
            });
        },

        _initDragable: function () {

            var $this = this;

            $this.newElelements.draggable({
                connectToSortable: ".jsElementList",

                handle: ".jsSort",
                scroll: false,
                zIndex: 1000,

                helper: function () {
                    var $newElem = $(this).clone();
                    $newElem.find(".jsConfig").remove();
                    return $newElem;
                },

                drag: function () {
                    $this._emptyList();
                },

                start: function (event, ui) {
                    $(this).addClass("jsAssigning");
                    $(this).data("config", $(this).find(".jsConfig").remove());
                    ui.helper.addClass("ghost");
                },

                stop: function (event, ui) {
                    $(this).removeClass("jsAssigning");
                    ui.helper.removeClass("ghost");
                    $(this).append($(this).data("config"));
                    $this._emptyList();
                    $this._rebuildList();
                }
            });
        },

        _emptyList: function () {
            var $this = this;

            $this.$(".jsElementList:not(.unassigned)").each(function () {

                var $list = $(this),
                    $emptyLists = $list.hasClass("empty-list"),
                    $notSorts = $list.children(":not(.ui-sortable-helper)").length;

                if ($emptyLists && $notSorts || !$emptyLists && !$notSorts) {
                    $list.toggleClass("empty-list");
                }
            });
        },

        _rebuildList: function () {

            var $this = this,
                regReplace = new RegExp(/(tmp\[[a-z0-9_-]+\]\[[a-z0-9_-]+\])|(positions\[[a-z0-9_-]+\]\[[a-z0-9_-]+\])/);

            $this.editableLists.each(function () {
                var $position = $(this),
                    positionName = "positions[" + $position.data("position") + "]";

                $('.jsElement', this).each(function (positionIndex, elementBlock) {

                    var $element = $(this),
                        elementId = $('.jsElementId', $element).val();

                    $element.find("[name]").each(function () {

                        var $input = $(this),
                            oldName = $input.attr("name"),
                            replaceTo = positionName + "[" + elementId + "]";

                        if ($this.options.isElementTmpl) {
                            replaceTo = positionName + "[" + positionIndex + "]";
                        }

                        $input.attr("name", oldName.replace(regReplace, replaceTo));
                    });
                });
            });
        },

        noElements: function ($elementList) {

            var $this = this;

            $elementList.find(".jsNoElements").remove();

            if ($this.el.children(".jsElement").length == 0) {
                $("<li>")
                    .addClass("jsNoElements")
                    .text($this.options.textNoElements)
                    .appendTo($elementList);
            }
        },

        'mousedown .jsSort': function (e, $this) {
            $this.$(".jsElement").addClass("hideconfig");
        },

        'click .jsEdit': function (e, $this) {
            var $element = $(this).closest(".jsElement"),
                isHide = $element.is('.hideconfig');

            $this.hideAllConfigs();

            if (isHide) {
                $element.removeClass('hideconfig');
            } else {
                $element.addClass('hideconfig');
            }
        },

        'click .jsDelete': function (e, $this) {

            var $button = $(this);

            $this.confirm($this.options.textElementRemove, function () {

                $button.closest(".jsElement").slideUp(300, function () {
                    $(this).remove();
                    $this._emptyList();
                    $this._rebuildList();
                });
            });
        },

        'click .jsAddNewElement': function (e, $this) {

            var $link = $(this),
                type = $link.data('type'),
                group = $link.closest('.jsElementsGroup').data('group'),
                $elementList = $this.$('.jsElementList:first'),
                $place = $("<li>").addClass("element loading").appendTo($elementList);

            $elementList.removeClass('empty-list');

            $this.ajax({
                'url'     : $this.options.urlAddElement,
                'data'    : {
                    elementType : type,
                    elementGroup: group,
                    count       : 0
                },
                'dataType': 'html',
                'success' : function (data) {

                    var $newElement = $(data),
                        elemHeight = $newElement.height();

                    $this.hideAllConfigs();
                    $newElement.removeClass('hideconfig').hide();

                    // evalate
                    $newElement.find("script").each(function () {
                        eval($(this).text());
                    });

                    $place
                        .removeClass("loading")
                        .css('min-height', elemHeight)
                        .replaceWith($newElement);

                    $newElement.find(".hasTip").each(function () {
                        var title = $(this).attr('title').split('::');
                        if (title[1]) {
                            $(this).attr('title', title[1]);
                        } else {
                            $(this).attr('title', title[0]);
                        }
                    });

                    new Tips($newElement.find(".hasTip[title]").get(), {
                        maxTitleChars: 1000,
                        fixed        : false
                    });

                    $this.el.trigger("element.added", $place);
                    $newElement.fadeIn(300, function () {
                        $(this).effect("highlight", {}, 1000)
                    });

                    $this._rebuildList();
                }
            });

        },

        hideAllConfigs : function() {
            this.$(".jsElement").addClass("hideconfig");
        }

    });

})(jQuery, window, document);