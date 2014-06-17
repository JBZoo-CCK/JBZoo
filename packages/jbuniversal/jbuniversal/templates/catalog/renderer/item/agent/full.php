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
<div class="full_element">

    <?php if ($this->checkPosition('title')) : ?>
        <h1 class="title"><?php echo $this->renderPosition('title'); ?></h1>
    <?php endif; ?>

    <?php if ($this->checkPosition('company')) : ?>
        <h2><?php echo $this->renderPosition('company'); ?></h2>
    <?php endif; ?>

    <?php if ($this->checkPosition('image')) : ?>
        <div class="photo-agent"><?php echo $this->renderPosition('image'); ?></div>
    <?php endif; ?>

    <?php if ($this->checkPosition('text')) : ?>
        <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
    <?php endif; ?>

    <?php if ($this->checkPosition('list')) : ?>
        <ul class="list conact-agent"><?php echo $this->renderPosition('list', array('style' => 'list')); ?></ul>
    <?php endif; ?>

    <div class="clr clear"></div>
</div>
