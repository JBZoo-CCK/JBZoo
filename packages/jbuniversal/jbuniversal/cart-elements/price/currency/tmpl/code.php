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

if (count($list) > 1) : ?>
    <div class="jbprice-currency-list not-paid-box jsCurrencyList" data-default="<?php echo $default; ?>">
        <?php foreach ($list as $currency) : ?>
            <span class="jsPriceCurrency jbcurrency
                  <?php echo $currency == $default ? ' active' : '' ?>"
                  data-currency="<?php echo $currency; ?>"
                  title="<?php echo JText::_('JBZOO_JBCURRENCY_' . $currency); ?>"><?php echo strtoupper($currency); ?></span>
        <?php endforeach; ?>
        <div class="clear clr"></div>
    </div>
<?php endif;