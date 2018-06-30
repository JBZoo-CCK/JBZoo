/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.CurrencyModuleSwitcher',
        {
            'target': '.jbzoo'
        },
        {
            init: function ($this) {
                var curCurrency = $this.getCookie('current', $this.getCurrent(), 'JBZooCurrencyToggle');

                $this.toggle(curCurrency);
            },

            getCurrent: function () {
                return this.$('input:checked,select').val();
            },

            toggle: function (newCurrency) {
                var $this = this;

                $this._getMoney().JBZooMoney('convert', [newCurrency]);
                $this.setCookie('current', newCurrency, 'JBZooCurrencyToggle');
                $this._trigger('change.JBZooCurrencyToggle', '{document} .jsCurrencyToggle', [newCurrency]);
            },

            'change input,select': function (e, $this) {
                $this.toggle($(this).val());
            },

            _getMoney: function () {
                var $this = this;

                return $(".jsMoney", $($this.options.target)).filter(function () {
                    return $(this).closest('.jsNoCurrencyToggle').length == 0;
                }).JBZooMoney();
            }
        }
    );

})(jQuery, window, document);
