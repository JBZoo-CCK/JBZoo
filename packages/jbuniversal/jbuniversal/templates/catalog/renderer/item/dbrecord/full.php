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

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php if ($this->checkPosition('properties')) : ?>
    <h3>Detail information</h3>
    <table class="jbtable">
        <?php echo $this->renderPosition('properties', array('style' => 'jbtable')); ?>
    </table>
<?php endif; ?>


<?php if ($this->checkPosition('text')) : ?>
    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
<?php endif; ?>


<?php if ($this->checkPosition('others')) : ?>
    <h3>Others</h3>
    <table class="jbtable">
        <?php echo $this->renderPosition('others', array('style' => 'jbtable')); ?>
    </table>
<?php endif; ?>

<div class="clear clr"></div>
