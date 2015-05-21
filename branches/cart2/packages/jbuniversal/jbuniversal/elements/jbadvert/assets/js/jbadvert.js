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

    JBZoo.widget('JBZoo.JBAdvert', {
        'text_exec_alert': 'Are you sure?'
    }, {

        'change .param-list input[type=radio]': function (e, $this) {
            dump(1);
            if ($(this).val() == 2) {
                $this.alert($this.options.text_exec_alert);
            }

        }

    });

})(jQuery, window, document);
