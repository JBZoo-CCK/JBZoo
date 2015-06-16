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

    JBZoo.widget('JBZoo.PriceElement', {},
        {
            rePaint: function (data) {
                var $this = this;
                if (typeof data == 'array' || typeof data == 'object') {
                    $.each(data, function (i, html) {
                        $this._rePaint(html, $.trim(i));
                    });
                }
                else {
                    $this._rePaint(data);
                }
            },

            _rePaint: function (data, selector) {
                var container = JBZoo.empty(selector) ? this.el : $('.' + selector, this.el.closest('.jsPrice'));+

                container.empty().prepend($(data).contents());
            },

            _format: function (name) {

                var value = this.el.data(name.toLowerCase());
                value = this._ucfirst(value);

                return value;
            },

            _ucfirst: function (string) {
                string = "" + string;

                string = string.charAt(0).toUpperCase() + string.substr(1);

                return string;
            },

            /**
             * @returns JBZooPrice|boolean
             * @private
             */
            _getPriceWidget: function() {
                if(this.isWidgetExists('JBZooPrice')) {
                    return this.el.closest('.jsPrice').data('JBZooPrice');
                }

                return false;
            }
        }
    );

})(jQuery, window, document);