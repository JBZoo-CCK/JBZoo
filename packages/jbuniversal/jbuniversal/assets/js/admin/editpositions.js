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
     * @author YOOtheme.com
     * @author JBZoo.com
     * @param options
     * @constructor
     */
    $.fn.JBZooEditPositions = function (options) {

        var $this = $(this),
            defaultOptions = {
                'urlAddElement'    : "index.php?option=com_zoo",
                'textNoElements'   : "No elements",
                'textElementRemove': "Are you sure you want to delete the element?"
            },
            options = $.extend({}, defaultOptions, options),
            $allLists = $(".jsElementList", $this),
            $editableLists = $(".jsElementList:not(.unassigned)", $this),
            $newElelements = $(".jsElement", $(".jsElementList.unassigned"));

        $this.emptyList = function () {

            $(".jsElementList:not(.unassigned)", $this).each(function () {

                var $list = $(this),
                    $emptyLists = $list.hasClass("empty-list"),
                    $notSorts = $list.children(":not(.ui-sortable-helper)").length;

                if ($emptyLists && $notSorts || !$emptyLists && !$notSorts) {
                    $list.toggleClass("empty-list");
                }
            });
        };

        $this.rebuildList = function () {
            var regReplace = new RegExp(/(tmp\[[a-z0-9_-]+\]\[[a-z0-9_-]+\])|(positions\[[a-z0-9_-]+\]\[[a-z0-9_-]+\])/);

            $editableLists.each(function () {
                var $position = $(this),
                    positionName = "positions[" + $position.data("position") + "]";

                $('.jsElement', this).each(function () {

                    var $element = $(this),
                        elementId = $('.jsElementId', $element).val();

                    $element.find("[name]").each(function () {

                        var $input = $(this),
                            oldName = $input.attr("name"),
                            newName = oldName.replace(regReplace, positionName + "[" + elementId + "]");

                        $input.attr("name", newName);
                    });
                });
            });
        };

        $this.noElements = function ($elementList) {

            $elementList.find(".jsNoElements").remove();
            if ($this.children(".jsElement").length == 0) {
                $("<li>").addClass("jsNoElements").text(options.textNoElements).appendTo($elementList)
            }
        };

        $allLists
            .delegate(".jsSort", "mousedown", function () {
                $(".jsElement", $this).addClass("hideconfig");
            })
            .delegate(".jsEdit", "click", function () {
                $(this).closest(".jsElement").toggleClass("hideconfig");
            })
            .delegate(".jsDelete", "click", function () {
                if (confirm(options.textElementRemove)) {
                    $(this).closest(".jsElement").slideUp(300, function () {
                        $(this).remove();
                        $this.emptyList();
                        $this.rebuildList();
                    });
                }
            });

        $editableLists.each(function (n, list) {
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
                    $this.emptyList();
                },

                update: function (event, ui) {

                    if (ui.item.hasClass("jsAssigning")) {

                        $this.find(".jsAssigning").each(function () {

                            if ($(this).data("config")) {

                                var $newElem = $(this).data("config").clone();

                                $newElem.find("input:radio").each(function () {
                                    var newAttrs = $(this).attr("name").replace(/^elements\[[\w_-]+\]/, "elements[_tmp]");

                                    $(this).attr("name", newAttrs);
                                });

                                ui.item.append($newElem);
                            }
                        });

                        ui.item.removeClass("jsAssigning");
                    }

                    $this.emptyList();
                },

                start: function (e, ui) {
                    ui.helper.addClass("ghost")
                },

                stop: function (e, ui) {
                    ui.item.removeClass("ghost");
                    $this.emptyList();
                    $this.rebuildList();
                }
            });
        });

        $newElelements
            .draggable({
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
                    $this.emptyList();
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
                    $this.emptyList();
                    $this.rebuildList();
                }
            });

        $(".jsAddNewElement", $this).bind("click", function () {

            var $link = $(this),
                type = $link.data('type'),
                group = $link.closest('.jsElementsGroup').data('group'),
                $elementList = $('.jsElementList:first', $this),
                $place = $("<li>").addClass("element loading").prependTo($elementList);

            $elementList.removeClass('empty-list');

            JBZoo.ajax({
                'url'     : options.urlAddElement,
                'data'    : {
                    elementType : type,
                    elementGroup: group,
                    count       : 0
                },
                'dataType': 'html',
                'success' : function (data) {
                    var $newElement = $(data),
                        elemHeight = $newElement.height();

                    $newElement.removeClass('hideconfig').hide();

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

                    $this.trigger("element.added", $place);
                    $newElement.fadeIn(300, function () {
                        $(this).effect("highlight", {}, 1000)
                    });

                    $this.rebuildList();
                }
            });

        });

        $this.emptyList();
        $this.rebuildList();
    };

})(jQuery, window, document);