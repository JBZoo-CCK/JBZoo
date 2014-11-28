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
            'target': '.jbzoo',
            'rates' : {}
        },
        {
            /**
             * @returns JBZooMoney
             * @private
             */
            _getMoney: function () {
                var $this = this;

                return $('.jsMoney', $($this.options.target)).JBZooMoney({
                    'rates': $this.options.rates
                });
            },

            'change .jbcurrency-input': function (e, $this) {
                var currency = $(this).data('currency');

                $this._getMoney().JBZooMoney('convert', [currency]);
            }
        }
    );

})(jQuery, window, document);
