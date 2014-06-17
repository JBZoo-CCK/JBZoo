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


$viewMode = (int)$params->get('balance_show', 1);

?>

<?php if ($viewMode) : ?>
    <div class="jbprice-balance">

        <span class="balance <?php echo $this->_getHash(); ?>" style="display: inline;">
            <?php echo $this->_getBalanceText($basic['balance'], $viewMode, ElementJBPriceAdvance::TYPE_PRIMARY); ?>
        </span>

        <?php foreach ($variations as $variant) : ?>
            <span class="balance <?php echo $variant['hash']; ?>">
            <?php echo $this->_getBalanceText($variant['balance'], $viewMode, ElementJBPriceAdvance::TYPE_SECONDARY); ?>
        </span>
        <?php endforeach; ?>

    </div>
<?php endif; ?>
