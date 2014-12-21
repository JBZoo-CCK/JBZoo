/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.EmailPreview', {
        'url': ''
    }, {
        'click .jsEmailTmplPreview': function (e, $this) {
            $this.$('#jsOrderList').toggle();
        },

        'click #jsOrderList .order-id': function (e, $this) {

            SqueezeBox.initialize({});
            SqueezeBox.open($this.options.url + '&id=' + $(this).data('id'), {
                handler: 'iframe',
                size   : {x: 1050, y: 700}
            });

            return false;
        }

    });

})(jQuery, window, document);