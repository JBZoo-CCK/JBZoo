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
?>
    <table <?php echo $this->getAttrs(array(
            'cellpadding' => 8,
            'width'       => '100%'
        )) .
        $this->getStyles(array(
            'border-collapse' => 'collapse',
            'font-size'       => '14px',
            'width'           => '100%'
        )); ?>>

        <?php if ($this->checkPosition('title')) : ?>
            <tr>
                <td colspan="2">
                    <h2><?php echo $this->renderPosition('title'); ?></h2>
                    <hr>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="2" valign="top">
                <?php echo $this->renderPosition('items', array('style' => 'block')); ?>
            </td>
        </tr>

        <tr>
            <td valign="top" width="50%">
                <?php echo $this->renderPosition('info', array('style' => 'block')); ?>
                <?php echo $this->renderPosition('payment', array('style' => 'block')); ?>
            </td>
            <td valign="top">
                <?php echo $this->renderPosition('shipping', array('style' => 'block')); ?>
            </td>
        </tr>

    </table>

<?php if ($this->checkPosition('other')) : ?>
    <table>
        <?php echo $this->renderPosition('other', array('style' => 'table-row')); ?>
    </table>
<?php endif;
