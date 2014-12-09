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


    JBZoo.widget('JBZoo.CompareButtons', {}, {

        'click .jsCompareToggle': function (e, $this) {

            var $toggle = $(this);

            $this.ajax({
                'url': $toggle.attr("href"),

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

            return false;
        }

    });

})(jQuery, window, document);