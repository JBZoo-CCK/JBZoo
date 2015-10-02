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

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.CurrencyToggle',
        {
            'target'    : '.jbzoo',
            'rates'     : {},
            'defaultCur': 'default_cur',
            'setOnInit' : false,
            'isMain'    : false
        },
        {
            'rates': {},

            init: function ($this) {

                if (JBZoo.empty($this.options.rates)) {
                    $this.options.rates = JBZoo.getVar('currencyList', {});
                }

                var curCurrency = $this.getCurrent();

                if ($this.options.setOnInit) {
                    $this._getMoney().JBZooMoney('convert', curCurrency, false);
                    $this._trigger('change', [curCurrency]);
                }

                if ($this.options.isMain) {
                    var newCurrency = $this.getCookie('current', curCurrency);

                    if (curCurrency != newCurrency) {
                        $this.setCurrency(newCurrency);
                        $this.toggle();
                    }
                }

                $this.setCookie('current', curCurrency);
            },

            /**
             * Get current currency
             * @returns {*}
             */
            getCurrent: function () {

                var $this = this,
                    $checked = $this.$('.jbcurrency-input:checked');

                if ($checked.length > 0) {
                    return $checked.data('currency');
                }

                return $this.options.defaultCur;
            },

            setCurrency: function (newCurrency) {
                var $this = this;

                if (newCurrency) {
                    var $input = $this.$('.jbcurrency-' + newCurrency)
                    if ($input.length) {
                        $input.prop('checked', true);
                    } else {
                        $this.$('.jbcurrency-input').removeAttr('checked');
                    }
                }
            },

            toggle: function () {
                this._getMoney().JBZooMoney('convert', this.getCurrent());
            },

            /**
             * @returns JBZooMoney
             * @private
             */
            _getMoney: function () {
                var $this = this;

                return $(".jsMoney", $($this.options.target)).filter(function () {
                    return $(this).closest('.jsNoCurrencyToggle').length == 0 || $(this).is('.jsNoCurrencyToggle');
                }).JBZooMoney({'rates': $this.options.rates});
            },

            'change .jbcurrency-input': function (e, $this) {
                var newCurrency = $(this).data('currency');

                $this._getMoney().JBZooMoney('convert', [newCurrency]);

                if ($this.options.isMain) {
                    $this.setCookie('current', newCurrency);
                    $this._trigger('change', [newCurrency]);
                }
            },

            'change.JBZooCurrencyToggle {document} .jsCurrencyToggle': function (event, $this, newCurrency) {

                if ($this.getCurrent() != newCurrency) {
                    $this.setCurrency(newCurrency);
                }
            }

        }
    );

})(jQuery, window, document);
