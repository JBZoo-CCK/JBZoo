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


defined('_JEXEC') or die('Restricted access');

?>

<fieldset class="pos-content creation-form">
    <legend>Summary</legend>
    <?php if ($this->checkPosition('main')) : ?>
        <?php echo $this->renderPosition('main', array('style' => 'submission.block')); ?>
    <?php endif; ?>
</fieldset>

<fieldset class="pos-content creation-form">
    <legend>Options Apartment / House</legend>
    <?php if ($this->checkPosition('properties')) : ?>
        <?php echo $this->renderPosition('properties', array('style' => 'submission.block')); ?>
    <?php endif; ?>
</fieldset>
