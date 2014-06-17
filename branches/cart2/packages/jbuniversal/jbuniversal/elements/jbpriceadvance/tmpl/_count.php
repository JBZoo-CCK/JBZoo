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


$uniqid = uniqid('jbprice-count-');

?>

<?php if ($isEnabled) : ?>
    <div class="jbprice-count">
        <label for="<?php echo $uniqid; ?>">
            <table cellpadding="0" cellspacing="0" border="0" class="no-border">
                <tbody>
                <tr>
                    <td rowspan="2" style="width: 104px;">
                        <?php echo JText::_('JBZOO_JBPRICE_COUNT_VALUE'); ?>:
                    </td>
                    <td rowspan="2">
                        <input type="text" id="<?php echo $uniqid; ?>" class="jsCount" value="1" maxlength="6"/>
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
