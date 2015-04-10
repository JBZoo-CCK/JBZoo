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

    JBZoo.widget('JBZoo.PriceElement_value', {},
        {
            rePaint: function (data) {

                this.el.empty().prepend($(data).contents());
                var $jbPrice = this.el.closest('.jsPrice'),
                    toggle = $('.jsCurrencyToggle', $jbPrice);

                if (toggle.length) {
                    toggle.JBZooCurrencyToggle('toggle');
                }
            }
        }
    );

})(jQuery, window, document);