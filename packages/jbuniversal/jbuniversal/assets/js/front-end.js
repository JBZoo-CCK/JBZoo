/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

;
(function ($, window, document, undefined) {

    jQuery(function ($) {

        // Goto link by button click
        $(document).on('click', '.jbzoo .jsGoto', function () {
            var url = $(this).attr('href');
            if (!url) {
                url = $(this).data('href');
            }

            parent.location.href = url;
            return false;
        });
    });

})(jQuery, window, document);
