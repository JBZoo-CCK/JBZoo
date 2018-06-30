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

    JBZoo.widget('JBZoo.PriceEditElement_descriptionEdit', {},
        {
            init: function () {
                this.html(this.el.val());
            },

            'change {element}': function (e, $this) {
                $this.html($(this).val());
            },

            html: function (value) {
                this.el.closest('.jsVariant').find('.jsVariantLabel .description').html(value);
            }

        }
    );

})(jQuery, window, document);