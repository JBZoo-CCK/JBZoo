<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$unique = $this->app->jbstring->getId('sku-');
$sku    = (!empty($value) ? $value : $this->getJBPrice()->getItem()->id);

if ($sku) : ?>

    <div class="jbprice-sku">
        <span class="field-name">
            <?php echo JText::_('JBZOO_JBPRICE_SKU'); ?>:
        </span>

        <span class="sku"><?php echo $sku; ?></span>
    </div>

<?php endif;