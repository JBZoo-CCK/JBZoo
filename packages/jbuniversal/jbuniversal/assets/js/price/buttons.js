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

    JBZoo.widget('JBZooPrice.Element_buttons',
        {
            'add'   : '',
            'remove': '',
            'modal' : ''
        },
        {
            // default data
            'image': '',
            'link' : '',

            init: function ($this) {

                this.on('click', '.jsAddToCart', function () {
                    $this.add();
                });

                this.on('click', '.jsRemoveFromCart', function () {
                    $this.remove();
                });
            },

            add: function () {

                this.ajax({
                    'url'    : this.options.add,
                    'data'   : {
                        "args": {
                            'quantity': $('.jsQuantity', this.constructor.parent.el).val(),
                            'values'  : this.constructor.parent.getValues()
                        }
                    },
                    'success': function (data) {

                    },
                    'error'  : function (data) {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });

            },

            remove: function () {

                this.ajax({
                    'url'    : $this.options.remove,
                    'data'   : {
                        "args": {
                            'quantity': $('.jsQuantity', this.constructor.parent.el).val(),
                            'values'  : this.constructor.parent.getValues()
                        }
                    },
                    'success': function (data) {

                    },
                    'error'  : function (data) {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });
            },

            modal: function () {

            }
        }
    );

})(jQuery, window, document);