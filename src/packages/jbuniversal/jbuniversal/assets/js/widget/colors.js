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

    JBZoo.widget('JBZoo.Colors',
        {
            'multiple': true,
            'type'    : 'radio'
        },
        {
            init: function () {
                this.el.find('input[type=' + this.options.type + ']:checked').next().addClass('checked');
            },

            reset: function () {

                var $this = this;

                $this.$('.jbcolor-input')
                    .removeAttr('checked')
                    .addClass('unchecked')
                    .removeClass('checked')
                    .next()
                    .removeClass('checked');
            },

            'click .jbcolor-input': function (e, $this) {

                var $field = $(this);

                if (!$this.options.multiple) {
                    if ($field.hasClass('checked')) {
                        $field
                            .removeAttr('checked')
                            .addClass('unchecked')
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $field.trigger('change');
                    } else {
                        $this.$('.jbcolor-input').removeClass('checked');
                        $this.$('.jbcolor-label').removeClass('checked');

                        $field
                            .attr('checked', true)
                            .addClass('checked')
                            .removeClass('unchecked')
                            .next()
                            .addClass('checked');
                    }
                } else {

                    if ($field.hasClass('checked')) {
                        $field
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $field.trigger('change');
                    } else {
                        $field
                            .addClass('checked')
                            .next()
                            .addClass('checked');
                    }

                }
            }
        }
    );

})(jQuery, window, document);