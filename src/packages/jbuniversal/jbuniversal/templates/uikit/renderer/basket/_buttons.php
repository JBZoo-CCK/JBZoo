<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$isCreate  = (int)$view->config->get('tmpl_button_create', 1);
$isPayment = (int)$view->config->get('tmpl_button_payment', 1) && $view->payment;

?>

<div class="uk-text-center jbform-actions uk-clearfix">

    <?php if ($isCreate) : ?>
        <input type="submit" name="create" value="<?php echo JText::_('JBZOO_CART_SUBMIT'); ?>"
               class="uk-button uk-button-large uk-button-success" />
    <?php endif; ?>

    <?php if ($isPayment) : ?>
        <input type="submit" name="create-pay" value="<?php echo JText::_('JBZOO_CART_SUBMIT_AND_PAY'); ?>"
               class="uk-button uk-button-large uk-button-success" />
    <?php endif; ?>

</div>
