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

    JBZoo.widget('JBZoo.PriceElement.Value', {},
        {
            rePaint: function (data) {
                this._parent('rePaint', [data]);
                var $jbPrice = this.el.closest('.jsPrice'),
                    toggle = $('.jsCurrencyToggle', $jbPrice);

                if (toggle.length) {
                    toggle.JBZooCurrencyToggle('toggle');
                } else {
                    toggle = $('.jsCurrencyToggle');
                    if (toggle.length) {
                        toggle.JBZooCurrencyToggle('toggle');
                    }
                }
            }
        }
    );

})(jQuery, window, document);