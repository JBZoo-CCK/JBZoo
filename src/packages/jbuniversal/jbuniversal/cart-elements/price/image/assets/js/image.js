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

    JBZoo.widget('JBZoo.PriceElement.Image',
        {
            'related' : '',
            'image'   : '',
            'default' : '',
            'duration': 100
        },
        {
            // default data
            'image': '',
            'link' : '',

            init: function () {
                var $jbzoo = this.el.closest('.jbzoo');

                var related = JBZoo.empty(this.options.related) ? "" : '.' + this.options.related;
                this.image = $('.jbimage' + related + ':first', $jbzoo);
                this.link = $('.jbimage-link' + related + ':first', $jbzoo);

                this.options.default = {
                    'image': this.image.attr('src'),
                    'popup': this.link.attr('href')
                };

                this.rePlace(this.options.image, null);
            },

            _rePaint: function (data) {
                this.rePlace(data, this.options.duration);
            },

            rePlace: function (data, duration) {
                if (JBZoo.empty(data)) {
                    data = this.options['default'];
                }

                if (!JBZoo.empty(data) && data.image != this.image.attr('src')) {

                    if (!JBZoo.empty(duration)) {
                        this.image.fadeOut(duration, function () {
                            $(this).attr('src', data.image).fadeIn();
                        });

                        return this.link.attr('href', data.popup);
                    }

                    this.image.attr('src', data.image);

                    if (this.image.hasClass('jbimage-gallery')) {
                        this.link.attr('href', data.popup);
                    }
                }
            }
        }
    );

})(jQuery, window, document);