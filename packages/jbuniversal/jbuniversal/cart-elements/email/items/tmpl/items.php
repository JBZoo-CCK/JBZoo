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

?>

<table <?php echo $this->getAttrs(array(
        'cellspacing' => 0,
        'cellpadding' => 8,
        'width'       => '100%'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse'
    )); ?>
    >

    <?php echo $this->_partial('table_head'); ?>

    <?php echo $this->_partial('table_body'); ?>

    <tfoot>
    <?php echo $this->_partial('table_foot_subtotal'); ?>
    <?php echo $this->_partial('table_foot_modifiers'); ?>
    <?php echo $this->_partial('table_foot_payment'); ?>
    <?php echo $this->_partial('table_foot_shipping'); ?>
    <?php echo $this->_partial('table_foot_total'); ?>
    </tfoot>

</table>
