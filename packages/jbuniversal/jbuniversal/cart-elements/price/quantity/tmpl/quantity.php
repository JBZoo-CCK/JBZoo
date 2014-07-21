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
            <table cellpadding="0" cellspacing="0" border="0" class="no-border">
                <tbody>
                <tr>
                    <td rowspan="2" style="width: 104px;">
                        <?php echo JText::_('JBZOO_JBPRICE_COUNT_VALUE'); ?>:
                    </td>
                    <td rowspan="2">
                        <div class="item-count-wrapper">
                            <div class="item-count">
                                <dl class="item-count-digits">
                                    <dd></dd>
                                    <dd></dd>
                                    <dd></dd>
                                    <dd></dd>
                                    <dd></dd>
                                </dl>
                                <input type="text" class="jsCount count" value="1" maxlength="6" id="<?php echo $unique; ?>"/>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="#plus" class="jsAddQuantity btn-mini plus" title="+"> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="#minus" class="jsRemoveQuantity btn-mini minus" title="-"> </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </label>
    </div>

<?php else : ?>
    <div class="count-value-wrapper">
        <?php echo JText::_('JBZOO_JBPRICE_COUNT_DEFAULT_VALUE'); ?>: <span class="jsCountValue">1</span>
    </div>
    <input type="hidden" class="jsCount" value="1"/>
<?php endif; ?>

