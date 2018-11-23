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


    JBZoo.widget('JBZoo.ShippingType.Ems', {}, {
    
        init: function ($this) {
            $this._initSelects();
        },

        'change select': function (e, $this) {
            var $select = $(this);

            if ($select.val()) {
                $this.$('select').not($select).JBZooSelect('disable');
            } else {
                $this.$('select').JBZooSelect('enable');
            }

            $this._updatePrice();
        },

        _initSelects: function () {
            var $this = this,
                $controls = $this.$('select');

            $controls.JBZooSelect('addChosen', {width: '95%'}); // init chosen widget
            $controls.each(function () {
                var $select = $(this);
                $select.JBZooSelect('toggle', !!$select.val());
            });

            if ($this.$('select:enabled').length == 0) {
                $controls.JBZooSelect('enable');
            }
        }

    });

})(jQuery, window, document);