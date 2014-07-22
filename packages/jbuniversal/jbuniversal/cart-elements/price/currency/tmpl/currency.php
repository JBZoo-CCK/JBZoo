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

if (count($currencyList) > 1) : ?>
    <div class="jbprice-currency-list not-paid-box">
        <?php foreach ($currencyList as $currency) : ?>
            <span class="jbprice-currency jsPriceCurrency jbcurrency jbcurrency-<?php echo strtolower($currency); ?>"
                  data-currency="<?php echo $currency; ?>"
                  title="<?php echo JText::_('JBZOO_JBCURRENCY_' . $currency); ?>"><?php echo $currency; ?></span>
        <?php endforeach; ?>
        <div class="clear clr"></div>
    </div>
<?php endif;