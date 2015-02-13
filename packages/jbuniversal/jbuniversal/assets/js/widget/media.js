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

    JBZoo.widget('JBZoo.Media', {
            'folder'             : '',
            'author'             : '',
            'message_open_editor': 'Open editor',

            // TODO optional preview image on hover
            'preview'            : false
        },
        {
            unique: {},
            url   : '',

            init: function ($this) {

                this.url = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

                this.unique = this._name + '_' + this._id;

                this.cancel();
                this.button();

                if (this.options.preview) {
                    this.preview();
                }

                if ($.isFunction(window.jInsertEditorText)) {
                    window[this.unique] = window.jInsertEditorText;
                }

                window.jInsertEditorText = function (img, id) {

                    if ($this.unique == id) {
                        var value = img.match(/src="([^\"]*)"/)[1];

                        $this.el
                            .find('.jsMediaPreview')
                            .html(img)
                            .find('img')
                            .attr("src", $this.url + value);

                        $this.$('.jsMediaValue').val(value);

                    } else {
                        $.isFunction(window[$this.unique]) &&
                        window[$this.unique](img, id);
                    }

                };
            },

            'click .jsMediaButton': function (e, $this) {
                e.preventDefault();

                SqueezeBox.fromElement(this, {
                    handler: "iframe",
                    url    : "index.php?option=com_media&view=images&tmpl=component&e_name=" + $this.unique + '&folder=' + $this.options.folder,
                    size   : {x: 850, y: 500}
                });
            },

            'click .jsMediaCancel': function (e, $this) {
                $(this).prev().val("");
                $this.$('.jsMediaPreview').empty();
            },

            preview: function () {

                this.el.append($('<div />', {
                        'class': 'jsMediaPreview image-preview'
                    }).append($('<img />', {
                        'class': 'jsMediaImgPreview',
                        'src'  : this.value()
                    }))
                );
            },

            button: function () {

                this.el.append($('<button>', {
                    'class': 'jbmedia-button jsMediaButton',
                    'text' : this.options.message_open_editor
                }));
            },

            cancel: function () {

                this.el.append($('<span>', {
                    'class': 'jbmedia-cancel image-cancel jsMediaCancel'
                }));
            },

            value: function () {
                var value = this.$('.jsMediaValue').val();

                return value ? this.url + value : '';
            }
        }
    );

})(jQuery, window, document);