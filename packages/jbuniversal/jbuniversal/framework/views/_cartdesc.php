<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($helpMsg = $this->app->jbhelp->hook('cart')) : ?>
    <div class="jbinfo">
        <div class="jbinfo-block-top">
            <?php echo $helpMsg; ?>
        </div>
    </div>
<?php endif;
