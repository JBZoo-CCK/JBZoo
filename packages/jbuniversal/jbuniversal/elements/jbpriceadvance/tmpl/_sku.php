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
<div class="jbprice-sku">
    <span class="field-name"><?php echo JText::_('JBZOO_JBPRICE_SKU'); ?>:</span>

    <span class="sku <?php echo $this->_getHash(); ?>" style="display: inline;"><?php echo $basic['sku']; ?></span>

    <?php foreach ($variations as $variant) : ?>
        <span class="sku <?php echo $variant['hash']; ?>"><?php echo $variant['sku']; ?></span>
    <?php endforeach; ?>

</div>
