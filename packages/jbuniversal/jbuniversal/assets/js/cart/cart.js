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
     * JBZoo Cart widget
     */
    JBZoo.widget('JBZoo.Cart', {
        quantityUrl : '',
        deleteUrl   : '',
        clearConfirm: '',
        clearUrl    : ''
    }, {
        shipping     : {},
        changeTimer  : 0,
        changeTimeout: 100,

        init: function ($this) {

            $this.shipping = $('.jbzoo .shipping-list').JBCartShipping({
                'no_value_message': 'Free'
            });

            $this.module = $('.jsJBZooCartModule').JBZooCartModule();

            $this.$('.jsQuantity').JBZooQuantity({
                'default' : 1,
                'step'    : 1,
                'decimals': 0
            });
        },

        changeCallback: function ($input) {

            var $this = this,
                value = parseFloat($input.val()),
                tr = $input.parents('.jsQuantityTable').closest('tr'),
                itemid = parseInt(tr.data('itemid'), 10),
                key = tr.data('key');

            if ($input.val().length && value > 0) {

                $this.ajax({
                    'url' : $this.options.quantityUrl + '&' + $this.shipping.getParams(),
                    'data': {
                        'value': value,
                        'key'  : key
                    },

                    'success': function (data) {
                        $this.recount(data);
                        $this.module.JBZooCartModule('reload');
                    },

                    'error': function (data) {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });
            }
        },

        addLoading: function () {
            this.el.addClass('loading', 100);
            this.$('input, select, textarea').attr('disabled', 'disabled');
        },

        removeLoading: function () {
            this.el.removeClass('loading', 100);
            this.$('input, select, textarea').removeAttr('disabled');
        },

        morphology: function (num, prfxs) {
            prfxs = prfxs || ['', 'а', 'ов'];
            num = '' + num;

            if (num.match(/^(.*)(11|12|13|14|15|16|17|18|19)$/)) {
                return prfxs[2];
            }
            if (num.match(/^(.*)1$/)) {
                return prfxs[0];
            }
            if (num.match(/^(.*)(2|3|4)$/)) {
                return prfxs[1];
            }
            if (num.match(/^(.*)$/)) {
                return prfxs[2]
            }

            return prfxs[0];
        },

        'recount': function (data) {

            var $this = this;

            if ($this.jbzoo.empty(data.items)) {

                for (var key in data.items) {
                    var subTotal = data.items[key],
                        row = $this.$('.row-' + key),
                        elem = $this.$('.row-' + key + ' .jsSubtotal .jsValue');

                    (elem).reCount(subTotal.total, {
                        decimals: 2
                    });
                }

            }

            var count = $this.$('.jsTotalCount .jsValue'),
                total = $this.$('.jsTotalPrice .jsValue'),
                morph = $this.$('.jsMorphology'),
                word = morph.data('word');

            morph.html(word + $this.morphology(data.count));

            $(count).reCount(data.count, {
                'decimals': 1,
                'duration': 100
            });

            $(total).reCount(data.total);
            $this.shipping.setPrices(data);
        },

        'click .jsDelete': function (e, $this) {

            var $button = $(this),
                itemid = $button.closest('tr').data('itemid'),
                key = $button.closest('tr').data('key');

            $this.addLoading();

            $this.ajax({
                'url'    : $this.options.deleteUrl,
                'data'   : {
                    'itemid'  : itemid,
                    'key'     : key,
                    'shipping': $this.shipping.getParams()
                },
                'success': function (data) {
                    var $row = $button.closest('tr');
                    $row.slideUp(300, function () {
                        $row.remove();
                        if ($this.$('tbody tr').length == 0) {
                            window.location.reload();
                        }
                    });

                    $this.recount(data);
                    $this.module.JBZooCartModule('reload');
                    $this.removeLoading();
                },
                'error'  : function (error) {
                    $this.removeLoading();
                }
            });

        },

        'click .jsDeleteAll': function (e, $this) {
            if (confirm($this.options.clearConfirm)) {
                $this.ajax({
                    'url'    : $this.options.clearUrl,
                    'success': function () {
                        window.location.reload();
                    }
                });
            }
        },

        'keyup .jsQuantity': function (e, $this) {
            var $input = $(this);
            clearTimeout($this.changeTimer);
            $this.changeTimer = setTimeout(function () {
                $this.changeCallback($input);
            }, $this.changeTimeout);
        },

        'change .jsQuantity': function (e, $this) {
            var $input = $(this);
            clearTimeout($this.changeTimer);
            $this.changeTimer = setTimeout(function () {
                $this.changeCallback($input);
            }, $this.changeTimeout);
        }

    });

})(jQuery, window, document);