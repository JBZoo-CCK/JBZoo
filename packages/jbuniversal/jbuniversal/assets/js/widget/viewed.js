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

    /**
     * @param options
     */
    $.fn.JBZooViewed = function (options) {

        var options = $.extend({}, {
            'message': 'Do you really want to delete the history?',
            'app_id' : ''
        }, options);
        var $this = $(this);

        if ($this.hasClass('module-items-init')) {
            return $this;
        } else {
            $this.addClass('module-items-init');
        }

        return $this.find('.jsRecentlyViewedClear').on('click', function () {

            JBZoo.confirm(options.message, function () {
                JBZoo.ajax({
                    'data'    : {
                        'controller': 'viewed',
                        'task'      : 'clear',
                        'app_id'    : options.app_id
                    },
                    'dataType': 'html',
                    'success' : function () {
                        $this.slideUp('slow');
                    }
                });
            });

            return false;
        });
    };

})(jQuery, window, document);
