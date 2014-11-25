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
            'change .jbcurrency-input': function (e, $this) {
            
                $money = $('.jsMoney', $($this.options.target)).JBZooMoney({
                    'rates': $this.options.rates
                });            
            
                $money.JBZooMoney('convert', $(this).data('currency'));
            }
        }
    );

})(jQuery, window, document);
