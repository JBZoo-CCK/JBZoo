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
        timeout: 300,
        element: '.column'
    }, {

        init: function ($this) {

            var maxHeight = 0;

            $this._delay(function () {

                $this.$($this.options.element).each(function (n, obj) {

                    var tmpHeight = JBZoo.int($(obj).height());

                    if (maxHeight < tmpHeight) {
                        maxHeight = tmpHeight;
                    }

                }).css({height: maxHeight});

            }, $this.options.timeout);
        }

    });

})(jQuery, window, document);
