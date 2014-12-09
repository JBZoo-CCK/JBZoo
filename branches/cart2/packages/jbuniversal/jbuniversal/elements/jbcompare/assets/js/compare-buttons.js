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

    JBZoo.widget('JBZoo.CompareButtons', {
        url_toggle: ''
    }, {

        'click .jsCompareToggle': function (e, $this) {

            $this.ajax({
                'url'    : $this.options.url_toggle,
                'target' : $(this),
                'success': function (data) {

                    if (data.status) {
                        $this.el.removeClass('unactive').addClass('active');

                    } else {

                        if (data.message) {
                            $this.alert(data.message);
                        }

                        $this.el.removeClass('active').addClass('unactive');
                    }
                }
            });
        }

    });

})(jQuery, window, document);