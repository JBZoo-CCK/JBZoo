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


    JBZoo.widget('JBZoo.ShippingType.RussianPost', {}, {

        init: function ($this) {
            $this.$('select').JBZooSelect('addChosen', {width: '95%'}); // init chosen widget
        },

        'change input[type=text],select': function (e, $this) {
            $this._delay(function () {
                $this._updatePrice();
            }, 300);
        }

    });

})(jQuery, window, document);