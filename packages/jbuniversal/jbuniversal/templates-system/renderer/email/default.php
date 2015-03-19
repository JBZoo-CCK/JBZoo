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


if ($this->checkPosition('title')) {
    echo '<h1>' . $this->renderPosition('title') . '</h1>';
}

if ($this->checkPosition('header')) {
    echo $this->renderPosition('header');
}

if ($this->checkPosition('body')) {
    echo $this->renderPosition('body');
}

if ($this->checkPosition('footer')) {
    echo $this->renderPosition('footer');
}
