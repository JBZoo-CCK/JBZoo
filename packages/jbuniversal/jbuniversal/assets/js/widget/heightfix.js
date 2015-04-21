/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /**
     * Height fix plugin
     */
    JBZoo.widget('JBZoo.HeightFix', {
        timeout: 500,
        element: '.column'
    }, {

        init: function ($this) {

            $this.updateSizes();

            // lisen document changes
            if ($this.options.timeout > 0) {
                setInterval(function () {
                    $this.updateSizes();
                }, $this.options.timeout);
            }
        },

        /**
         * Recalc all heights
         */
        updateSizes: function () {
            var $this = this,
                maxHeight = 0;

            $this.$($this.options.element)
                .css('height', 'auto')
                .each(function (n, obj) {
                    var tmpHeight = JBZoo.toInt($(obj).height());
                    if (maxHeight < tmpHeight) {
                        maxHeight = tmpHeight;
                    }
                })
                .css({height: maxHeight});
        }

    });

})(jQuery, window, document);
