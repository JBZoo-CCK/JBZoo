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
