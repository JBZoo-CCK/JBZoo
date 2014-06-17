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


$viewMode = (int)$params->get('only_balance_mode', 1);

?>

<?php if ($viewMode) : ?>
    <div class="jbprice-balance">

        <span class="balance" style="display: inline;">
            <?php echo $this->_getBalanceText($basic['balance'], $viewMode, ElementJBPriceAdvance::TYPE_PRIMARY); ?>
        </span>

    </div>
<?php endif; ?>
