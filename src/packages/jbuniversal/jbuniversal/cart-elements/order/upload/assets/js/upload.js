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

    JBZoo.widget('JBZoo.OrderUpload', {
        text_size_reached: '',
        max_size         : 524288 // 512 * 1024 bites
    }, {

        'change .jsInputUpload': function (e, $this) {
            var inputEl = this,
                $upload = $(this),
                value = $upload.val().replace(/^.*[\/\\]/g, '');

            $this.$('.jsFilename').val(value);

            // validate size
            var maxSize = JBZoo.toInt($this.options.max_size);
            if (maxSize > 0 && inputEl.files[0].size > maxSize) {
                $this.alert($this.options.text_size_reached);

                // cleanup values
                $upload.val('');
                $this.$('.jsFilename').val('');
                $this.$('.jsUploadFlag').val('');
            }

        }

    });

})(jQuery, window, document);

