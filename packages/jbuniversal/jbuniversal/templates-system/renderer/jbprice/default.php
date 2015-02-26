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

if ($this->checkPosition('right')) : ?>
    <div class="right">
        <?php echo $this->renderPosition('right'); ?>
    </div>
<?php endif;

if ($this->checkPosition('left')) : ?>
    <div class="left">
        <?php echo $this->renderPosition('left'); ?>
    </div>
<?php endif;
