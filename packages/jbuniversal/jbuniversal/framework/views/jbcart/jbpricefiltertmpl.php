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

?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_CART_' . $this->task); ?></h2>

        <?php echo $this->partial('cartdesc'); ?>

        <?php echo $this->partial('editpositions', array(
            'positions'      => $this->positions,
            'layoutList'     => $this->layoutList,
            'dragElements'   => $this->dragElements,
            'elementsParams' => $this->elementsParams,
            'systemElements' => $this->systemElements,
            'elementGroup'   => 'filter',
        ));?>

        <?php echo $this->partial('footer'); ?>
    </div>

    <div id="right-sidebar" class="uk-width-1-6">
        <?php echo $this->partial('right'); ?>
    </div>
</div>
