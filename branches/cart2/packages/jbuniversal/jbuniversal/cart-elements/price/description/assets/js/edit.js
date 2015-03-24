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

    JBZoo.widget('JBZoo.PriceEditElement_descriptionEdit', {},
        {
            init: function () {
                this.html(this.el.val());
            },

            'change {element}': function (e, $this) {
                $this.html($(this).val());
            },

            html: function (value) {
                this.el.closest('.jsVariant').find('.jsVariantLabel .description').html(value);
            }

        }
    );

})(jQuery, window, document);