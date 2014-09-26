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
$count = (int)$params->get('count_multiple', 1);
$start = (int)$params->get('count_default', 1);
$isEnabled = true;
?>

<?php if ($isEnabled) : ?>
    <div class="jbprice-quantity jbprice-count"
         data-multiple="<?php echo $count; ?>"
         data-default="<?php echo $start; ?>"
        >

        <label for="<?php echo $unique; ?>">
            <input type="text" name="<?php echo $this->getRenderName(); ?>"
                   class="jsCount count" value="1" maxlength="6" id="<?php echo $unique; ?>"/>

        </label>
    </div>

<?php else : ?>
    <div class="count-value-wrapper">
        <?php echo JText::_('JBZOO_JBPRICE_COUNT_DEFAULT_VALUE'); ?>: <span class="jsCountValue">1</span>
    </div>
    <input type="hidden" class="jsCount" value="1"/>
<?php endif;
