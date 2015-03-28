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
        'width'       => '800px'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse',
        'font-size'       => '14px',
        'width'           => '800px'
    )); ?>>

    <?php if ($this->checkPosition('title')) : ?>
        <tr>
            <td>
                <h2><?php echo $this->renderPosition('title'); ?></h2>
                <hr>
            </td>
        </tr>
    <?php endif; ?>

    <?php echo $this->renderPosition('body', array('style' => 'table-row')); ?>

</table>
