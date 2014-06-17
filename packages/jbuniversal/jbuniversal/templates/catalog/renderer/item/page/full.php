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


    <?php if ($this->checkPosition('text')) : ?>
        <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
    <?php endif; ?>


    <?php if ($this->checkPosition('related')) { ?>
        <h3><?php echo JText::_('JBZOO_TMPL_PAGE_SEE_ALSO'); ?>:</h3>
        <ul>
            <?php echo $this->renderPosition('related'); ?>
        </ul>
    <?php } ?>


    <?php if ($this->checkPosition('meta')) { ?>
        <h3><?php echo JText::_('JBZOO_TMPL_PAGE_SEE_ALSO'); ?>:</h3>
        <ul>
            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
        </ul>
    <?php } ?>


    <div class="clr"></div>
</div>		