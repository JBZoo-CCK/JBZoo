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


if ($this->checkPosition('billing')) : ?>

    <?php echo $this->renderPosition('billing'); ?>

<?php endif;

if ($this->checkPosition('shipping')) : ?>

    <?php echo $this->renderPosition('shipping'); ?>

<?php endif;

if ($this->checkPosition('payment')) : ?>

    <?php echo $this->renderPosition('payment'); ?>

<?php endif;

if ($this->checkPosition('other')) : ?>

    <?php echo $this->renderPosition('other'); ?>

<?php endif;
