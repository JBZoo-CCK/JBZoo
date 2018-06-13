/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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
