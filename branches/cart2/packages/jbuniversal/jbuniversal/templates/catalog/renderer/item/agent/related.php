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
<div class="clearfix">
    <h4 class="agent-title">LISTING PROVIDED BY</h4>
    <?php if ($this->checkPosition('rating')) : ?>
        <div class="agent-rating"><?php echo $this->renderPosition('rating'); ?></div>
    <?php endif; ?>
</div>

<div class="agent-teaser">
    <?php if ($this->checkPosition('photo')) : ?>
        <div class="agent-photo"><?php echo $this->renderPosition('photo'); ?></div>
    <?php endif; ?>

    <?php if ($this->checkPosition('title')) : ?>
        <div class="name"><?php echo $this->renderPosition('title'); ?></div>
    <?php endif; ?>

    <?php if ($this->checkPosition('contacts')) : ?>
        <ul class="contacts">
            <?php echo $this->renderPosition('contacts', array('style' => 'list')); ?>
        </ul>
    <?php endif; ?>
    <div class="clear clr"></div>

</div>