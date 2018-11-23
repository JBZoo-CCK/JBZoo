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