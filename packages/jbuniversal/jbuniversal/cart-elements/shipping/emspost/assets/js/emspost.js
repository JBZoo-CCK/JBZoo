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