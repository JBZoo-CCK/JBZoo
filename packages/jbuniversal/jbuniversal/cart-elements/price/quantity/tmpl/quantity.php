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

$unique = $this->app->jbstring->getId('quantity-');

$min = (float)$params->get('min', 0);
$count = (float)$params->get('step', 1);
$start = (float)$params->get('default', 1);
$decimals = (float)$params->get('decimals', 0);

$isEnabled = TRUE;

if ($isEnabled) : ?>
    <div class="jbprice-quantity jbprice-count"
         data-step="<?php echo $count; ?>"
         data-default="<?php echo $start; ?>"
         data-decimals="<?php echo $decimals; ?>"
         data-min="<?php echo $min; ?>"
        >

        <label for="<?php echo $unique; ?>">
            <input type="text" name="<?php echo $this->getRenderName('value'); ?>"
                   value="<?php echo $start; ?>" class="jsCount input-quantity count"
                   maxlength="6" id="<?php echo $unique; ?>"/>

        </label>
    </div>

<?php else : ?>
    <div class="count-value-wrapper">
        <?php echo JText::_('JBZOO_JBPRICE_COUNT_DEFAULT_VALUE'); ?>: <span class="jsCountValue">1</span>
    </div>
    <input type="hidden" class="jsCount" value="1"/>
<?php endif;
