<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$params    = $this->interfaceParams();
$unique    = $this->htmlId();
$default   = $params['default'];
$isEnabled = true;

if ($isEnabled) : ?>
    <div class="jbprice-quantity jbprice-count">
        <label for="<?php echo $unique; ?>">
            <?php echo $this->_jbhtml->quantity($default, $params, $unique, $this->getRenderName('value')); ?>
        </label>
    </div>
<?php else : ?>
    <div class="count-value-wrapper">
        <?php echo JText::_('JBZOO_JBPRICE_COUNT_DEFAULT_VALUE'); ?>: <span class="jsCountValue">1</span>
    </div>
    <input type="hidden" class="jsCount" value="1"/>
<?php endif;
