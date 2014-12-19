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

$unique    = $this->app->jbstring->getId('quantity-');
$isEnabled = true;

$default = (float)$params->get('default', 1);
$params  = array(
    'step'     => (float)$params->get('step', 1),
    'default'  => $default,
    'decimals' => (float)$params->get('decimals', 0),
    'min'      => (float)$params->get('min', 0),
    'max'      => 999999,
);

if ($isEnabled) : ?>
    <div class="jbprice-quantity jbprice-count">
        <label for="<?php echo $unique; ?>">
            <?php echo $this->app->jbhtml->quantity($default, $params, $unique, $this->getRenderName('value')); ?>
        </label>
    </div>

<?php else : ?>
    <div class="count-value-wrapper">
        <?php echo JText::_('JBZOO_JBPRICE_COUNT_DEFAULT_VALUE'); ?>: <span class="jsCountValue">1</span>
    </div>
    <input type="hidden" class="jsCount" value="1" />
<?php endif;
