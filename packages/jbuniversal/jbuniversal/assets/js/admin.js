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

/**
 * Scripts for Joomla CP
 */
jQuery(function ($) {

    /**
     * jQuery plugin for color element
     * @param options
     */
    $.fn.JBColorElement = function (options) {

        var options = $.extend({}, {
            message : ' already in the list settings',
            theme   : 'bootstrap',
            position: 'bottom'
        }, options);

        onAdded();

        function initMinicolors(element) {

            if ($.isFunction($.fn.minicolors)) {
                var $minicolors = $(element).find('.jbpicker .jbcolor');

                if ($minicolors.hasClass('jbcolor-colors-init')) {
                    return $minicolors;
                }

                $minicolors.minicolors({
                    theme   : options.theme,
                    position: options.position
                });

                $minicolors.addClass('jbcolor-colors-init');

            } else {

                $$('.jbpicker .jbcolor', element).each(function (item) {

                    if (item.hasClass('jbcolor-colors-init') || item.id == '') {
                        return item;
                    }

                    new MooRainbow(item, {
                        id        : item.id,
                        imgPath   : '../media/system/images/mooRainbow/',
                        startColor: [255, 0, 0],
                        onComplete: function (color) {
                            this.element.value = color.hex;
                        }
                    });
                    item.addClass('jbcolor-colors-init');

                });
            }
        }

        function onAdded() {
            $('#element-list').on('element.added', function (event, element) {
                initMinicolors(element);
            });
        }


        return $(this).each(function () {

            var $this = $(this);

            if ($this.hasClass('added-initialized')) {
                return $this;
            } else {
                $this.addClass('added-initialized');
            }

            initMinicolors($this);
            $('.jsColorAdd', $this).on('click', function () {

                var error = false,
                    $jbname = $('.jbname', $this),
                    $jbcolor = $('.jbcolor', $this),
                    name = $jbname.val(),
                    val = $jbcolor.val(),
                    color = val.toLowerCase(),
                    textVal = $.trim($('.jbcolor-textarea', $this).val()),
                    text = textVal.toLowerCase(),
                    space = text ? '\n' : '';

                if (color && text.indexOf(color) >= 0) {
                    alert(color + options.message);
                }

                if (!name.length) {
                    $jbname.addClass('error').focus();
                    error = true;
                }

                if (!color.length) {
                    $jbcolor.addClass('error').focus();
                    error = true;
                }

                if (error) {
                    return false;
                }

                $('.jbpicker input', $this).removeClass('error');

                $('.jbcolor-textarea', $this).val(text + space + name + color);
                $jbname.focus();
                $jbname.val('');
                $jbcolor.val('');
                $('.minicolors-swatch span', $this).removeAttr('style');

            });

            $('.jbcolor, .jbname', $this).on('keyup', function (event) {
                if (event.keyCode == 13) {
                    $('.jsColorAdd', $this).trigger('click');
                }
            });

        });

    };

    /**
     * Show/hide joomla field jbdelimiter
     */
    $.fn.JBZooDelimiter = function (options) {

        var options = $.extend({}, {
            'version': '3'
        }, options);

        var $mode = $('#jform_params_mode'),
            $parent = $(this).parents('#attrib-base'),
            group = $(this).attr('data-group');

        if (options.version == 2) {
            $parent = $(this).parents('.adminformlist');
        }

        $(this).each(function () {

            var $this = $(this);

            if (!$this.hasClass('jbdelimiter-init')) {
                $this.addClass('jbdelimiter-init');
            } else {
                return $this;
            }

            var $control = $this.parents('.control-group');

            if (options.version == 2) {
                $control = $this.parent('li');
            }

            $control.attr('data-group', group).addClass('stop');
            $control.nextUntil('.stop').attr('data-group', group);
        });

        var update = function (selected) {
            $parent.children().each(function () {
                group = $(this).attr('data-group');
                if (group != selected && typeof group != 'undefined' && group != 'close') {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }

        update($mode.val());

        if ($mode.hasClass('initialized')) {
            return $(this);
        } else {
            $mode.addClass('initialized');
        }

        $mode.on('change', function () {
            update($(this).val());
        });
    }

    $.fn.JBZooAdminMenu = function (options) {

        var $obj = $(this),
            html = '';

        if ($obj.is('.disabled')) {
            $obj.append('<li class="disabled"><a>' + options.name + '</a>');
        } else {

            $.each(options.items, function (parentKey, items) {

                var attrs = '';

                if (typeof items.target != "undefined") {
                    attrs += 'target="' + items.target + '"';
                }

                if (items == 'divider') {
                    html += '<li class="separator divider"><span></span></li>';

                } else if (typeof items.children == 'undefined' || items.children.length == 0) {
                    html += '<li><a ' + attrs + ' class="' + parentKey + '-item parent-link" href="' + items.url + '">' + items.name + '</a></li>';

                } else {

                    var classes = parentKey + '-item dropdown-toggle parent-link';
                    if (typeof items.icon != "undefined") {
                        attrs += ' style="background-image:url(' + items.icon + ');" ';
                    }

                    html += '<li class="node dropdown-submenu">';
                    html += '<a ' + attrs + ' class="' + classes + '" data-toggle="dropdown" href="' + items.url + '">' +
                        items.name + '</a><ul class="dropdown-menu">';

                    $.each(items.children, function (childKey, item) {

                        var innerAttrs = '';
                        if (typeof item.target != "undefined") {
                            innerAttrs += ' target="' + items.target + '" ';
                        }

                        if (item == 'divider') {
                            html += '<li class="separator divider"><span></span></li>';

                        } else {
                            html += '<li><a ' + innerAttrs + ' class="' + childKey + '-item" href="' + item.url + '">' + item.name + '</a></li>';
                        }
                    });

                    html += '</ul></li>';
                }

            });

            $obj.append('<li class="dropdown" id="jbzoo-adminmenu">' +
                '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' +
                options.name +
                ' <span class="caret"></span></a>' +
                '<ul class="dropdown-menu">' + html + '</ul></li>');
        }

    };

    /**
     * Pseudo jQuery plugin for form filed key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooKeyValue = function (options) {
        $('body').on('click', '.jsKeyValue .jsKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsKeyValue'),
                $template = $parent.find('.jbkeyvalue-row:first').clone(),
                length = $parent.find('.jbkeyvalue-row').length;

            $template.find('input').attr('value', '');

            html = '<div class="jbkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            return false;
        });
    }

    /**
     * Pseudo jQuery plugin for form filed joomla key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooJKeyValue = function (options) {

        $('body').on('click', '.jsJKeyValue .jsJKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsJKeyValue'),
                $template = $parent.find('.jbjkeyvalue-row:first').clone(),
                length = $parent.find('.jbjkeyvalue-row').length;


            $template.find('input').attr('value', '');
            $template.find('div').remove();
            $template.find('select').removeClass().show();
            $template.find('select option:selected').removeAttr('selected');

            if (length != 0) {
                $template.append('<a href="#jbjkeyvalue-rem" class="jsJKeyValueRemove">');
            }

            var html = '<div class="jbjkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('0key', (length) + 'key');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            if (typeof jQuery.fn.chosen !== 'undefined') {
                jQuery('.jbjkeyvalue-row:last select').chosen({
                    disable_search_threshold: 10,
                    allow_single_deselect   : true
                });
            }

            return false;
        });

        $('body').on('click', '.jsJKeyValue .jsJKeyValueRemove', function () {
            var $remButton = $(this),
                $row = $remButton.closest('.jbjkeyvalue-row'),
                $parent = $remButton.closest('.jsJKeyValue'),
                $pattern = /[0-9]+?/;

            $row.remove();

            $parent.find('.jbjkeyvalue-row').each(function (key, obj) {
                var $obj = $(obj),
                    $keyName = $('select', $obj).attr('name'),
                    $id = $('select', $obj).attr('id'),
                    $newName = $keyName.replace($pattern, (key)),
                    $newValue = $newName.replace('[key]', '[value]'),
                    $newId = $id.replace($pattern, (key));

                $('div', $obj).remove();
                $('select', $obj).removeClass().show().attr('name', $newName).attr('id', $newId);

                if (typeof jQuery.fn.chosen !== 'undefined') {
                    $('select', $obj).chosen('destroy');

                    jQuery('select', $obj).chosen({
                        disable_search_threshold: 10,
                        allow_single_deselect   : true
                    });
                }

                $('input', $obj).attr('name', $newValue);

            });

            return false;
        });
    }

    /**
     * Pseudo jQuery plugin for form filed ItemOrder
     * @param options
     * @constructor
     */
    $.fn.JBZooItemOrder = function (options) {
        $('body').on('click', '.jsItemOrder .jsItemOrderAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsItemOrder'),
                $template = $parent.find('.jbzoo-itemorder-row:first').clone(),
                length = $parent.find('.jbzoo-itemorder-row').length;

            $template.find('select option').removeAttr('selected');
            $template.find('input[type=checkbox]').removeAttr('checked');
            $template.find('label').removeAttr('for');

            var html = '<div class="jbzoo-itemorder-row">' + $template.html() + '</div><br>';
            html = html.split(/_jbzoo_[0-9]_/).join('_jbzoo_' + length + '_');

            $addButton.before(html);

            return false;
        });
    }

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

    $.fn.JBZooPriceAdvanceFields = function (options) {

        var options = $.extend({}, {
            'url': ''
        }, options);

        return $(this).each(function () {

            var $this = $(this),
                init = false;

            if (init) {
                return $this;
            }
            init = true;

            function refreshAll() {
                $('> .jbprice-fields-parameter', $this).each(function (i) {

                    var regParam = /^(\S+\[params\])\[\d+\](\[name\]|\[value\])$/,
                        $param = $(this);

                    $('> input', $param).each(function () {
                        var $input = $(this);

                        $input.attr('name', $input.attr('name').replace(regParam, "$1[" + i + "]$2"));
                        $input.removeAttr('disabled');
                    })

                    $('.jbprice-field-option', $param).each(function (k) {

                        var regOption = /^(\S+\[params\])\[\d+\](\[option\])\[\d+\](\[name\]|\[value\])$/,
                            $option = $(this);

                        $('input', $option).each(function () {
                            var $input = $(this);
                            $input.removeAttr('disabled');

                            $input.attr('name', $input.attr('name').replace(regOption, "$1[" + i + "]$2[" + k + "]$3"))
                        })

                    })
                })
            }

            function refreshParam($param) {

            }

            function bindFocusEvent($obj) {

                $('.jbprice-field-option', $obj).on('click', function () {
                    $('input', $(this)).focus();
                });
            }

            function bindAddOptionEvent($obj) {

                $('.jsJBPriceAddOption', $obj).on('click', function () {
                    var $divOption = $('.hidden .jbprice-field-option', $this).clone(),
                        $buttonOption = $(this);

                    $divOption.hide();
                    $buttonOption.prev().append($divOption);

                    bindDeleteOptionEvent($divOption);
                    bindFocusEvent($divOption.parent());
                    bindChangeAddValueEvent($divOption);

                    $divOption.slideDown('normal');

                    refreshAll();
                })
            }

            function bindDeleteParamEvent($obj) {

                $('.jsJBPriceDeleteParam', $obj).on('click', function () {

                    var yes = confirm('Yes/No ?');
                    if (yes) {
                        var $parent = $(this).parent();
                        $parent.slideUp('normal', function () {
                            $parent.remove();
                        });
                        refreshAll();
                    }

                    return false;
                })
            }

            function bindDeleteOptionEvent($obj) {

                $('.jsJBPriceDeleteOption', $obj).on('click', function () {

                    var yes = confirm('Yes/No ?');
                    if (yes) {
                        var $parent = $(this).parent();
                        $parent.slideUp('normal', function () {
                            $parent.remove();
                        });
                        refreshAll();
                    }

                    return false;
                })
            }

            function bindChangeAddValueEvent($obj) {

                $('.jsJBPriceOptionAddValue', $obj).on('change', function () {

                    var $input = $(this);
                    getValue($input);
                })

                $('.jsJBPriceParamAddValue', $obj).on('change', function () {

                    var $input = $(this);
                    getValue($input);
                })
            }

            function getValue($input) {
                $.ajax({
                    url     : options.url,
                    dataType: 'json',
                    data    : 'name=' + $input.val(),
                    success : function (value) {
                        $input.next().val(value);
                    }
                });
            }

            // Bind event on button to add new parameter
            $('.jsJBPriceAddParam', $this).on('click', function () {
                var $divParam = $('.hidden .jbprice-fields-parameter', $this).clone(),
                    $lastParam = $('>div:last', $this);

                bindAddOptionEvent($divParam);
                bindDeleteParamEvent($divParam);
                bindDeleteOptionEvent($divParam);
                bindChangeAddValueEvent($divParam);

                $divParam.hide();
                $divParam.insertAfter($lastParam);
                $divParam.slideDown("normal");

                refreshAll();
            });

            //Bind event on each button to add new option
            bindAddOptionEvent($this);

            //Bind event on each button to delete parameter
            bindDeleteParamEvent($this);

            //Bind event on each button to delete option
            bindDeleteOptionEvent($this);

            //Bind event on each input to get alias onChange
            bindChangeAddValueEvent($this);

            //Bind focus event on option's div
            bindFocusEvent($this);

            // Refresh all index's
            refreshAll();
        })

    }

    /**
     * Menu tabs hack
     */
    $('li[data-href-replace]').each(function (n, obj) {
        var $obj = $(obj),
            replace = $(obj).data('href-replace'),
            $link = $obj.children('a'),
            href = $link.attr('href');

        if (replace) {
            $link.attr('href', href.replace(replace, 'controller=item'));
        }
    });

    // init Joomla CP Scripts
    (function () {
        if (typeof JBAdminItems != 'undefined') {
            $('#menu').JBZooAdminMenu(JBAdminItems);
        }

        if ($("#nav [data-jbzooversion].active").length) {
            $('<span class="version" />')
                .text("JBZoo " + $("#nav [data-jbzooversion].active").data("jbzooversion"))
                .appendTo("#nav div.bar");
        }

        // some plugins
        $.fn.JBZooKeyValue();
        $.fn.JBZooJKeyValue();
        $.fn.JBZooItemOrder();
    }());

});
