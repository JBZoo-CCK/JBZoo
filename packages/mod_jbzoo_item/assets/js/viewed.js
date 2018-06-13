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
