<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// no direct access
defined('_JEXEC') or die('Restricted access');

if (isset($yaOption['yaEnabled'])) : ?>

    <div class="jbzoo-share">
        <div id="<?php echo $yaOption['element']; ?>"></div>

        <script type="text/javascript">
            new Ya.share(<?php echo json_encode($yaOption); ?>);
        </script>

    </div>

<?php endif ?>
