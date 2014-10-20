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

?>

<?php if ($this->checkPosition('title')) : ?>
    <h1><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php if ($this->checkPosition('header')) : ?>
    <?php echo $this->renderPosition('header'); ?>
<?php endif; ?>

<?php if ($this->checkPosition('body')) : ?>
    <?php echo $this->renderPosition('body'); ?>
<?php endif; ?>

<?php if ($this->checkPosition('footer')) : ?>
    <?php echo $this->renderPosition('footer'); ?>
<?php endif; ?>
