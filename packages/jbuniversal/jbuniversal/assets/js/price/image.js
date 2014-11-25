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

    JBZoo.widget('JBZooPrice.Element_image',
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
                this.image = $('.jbimage' + related, $jbzoo);
                this.link = $('.jbimage-link' + related, $jbzoo);

                this.options.default = {
                    'image' : this.image.attr('src'),
                    'pop_up': this.link.attr('href')
                };

                this.rePlace(this.options.image, null);
            },

            rePaint: function (data) {
                this.rePlace(data, this.options.duration);
            },

            rePlace: function (data, duration) {
                if (JBZoo.empty(data)) {
                    data = this.default;
                }

                if (!JBZoo.empty(data) && data.image != this.image.attr('src')) {

                    if (!JBZoo.empty(duration)) {
                        this.image.fadeOut(duration, function () {
                            $(this).attr('src', data.image).fadeIn();
                        });

                        return this.link.attr('href', data.pop_up);
                    }

                    this.image.attr('src', data.image);
                    this.link.attr('href', data.pop_up);
                }
            }
        }
    );

})(jQuery, window, document);