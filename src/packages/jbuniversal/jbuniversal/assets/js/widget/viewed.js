/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
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
