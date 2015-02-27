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

                    JBZoo.confirm('Yes/No ?', function(){
                        var $parent = $(this).parent();
                        $parent.slideUp('normal', function () {
                            $parent.remove();
                        });
                        refreshAll();
                    });

                    return false;
                })
            }

            function bindDeleteOptionEvent($obj) {

                $('.jsJBPriceDeleteOption', $obj).on('click', function () {

                    JBZoo.confirm('Yes/No ?', function(){
                        var $parent = $(this).parent();
                        $parent.slideUp('normal', function () {
                            $parent.remove();
                        });
                        refreshAll();
                    });

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

})(jQuery, window, document);