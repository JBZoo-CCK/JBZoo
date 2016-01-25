/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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