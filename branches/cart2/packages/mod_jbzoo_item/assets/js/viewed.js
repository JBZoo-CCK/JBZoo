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

    JBZoo.widget('JBZoo.Viewed', {
        'url_clear': '',
        'message'  : 'Do you really want to delete the history?',
    }, {

        'click .jsRecentlyViewedClear': function (e, $this) {

            $this.confirm($this.options.message, function () {
                $this.ajax({
                    'url'     : $this.options.url_clear,
                    'dataType': 'html',
                    'success' : function (data) {
                        $this.el.slideUp('slow');
                    }
                });
            });

            return false;
        }
    });

})(jQuery, window, document);
