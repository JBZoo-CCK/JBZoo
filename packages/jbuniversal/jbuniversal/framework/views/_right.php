<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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

<?php if ($helpMsg = $this->app->jbhelp->hook('cart', 'right')) : ?>
    <div class="jbinfo uk-panel uk-panel-box">
        <h3 class="jbinfo-header"><?php echo JText::_('JBZOO_CART_HELP_RIGHT') ?></h3>

        <div class="jbinfo-block-right">
            <?php echo $helpMsg; ?>
        </div>
    </div>
<?php endif;
