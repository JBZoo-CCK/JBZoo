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