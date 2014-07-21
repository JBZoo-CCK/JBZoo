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

$jbhtml  = $this->app->jbhtml;

$containerId = $this->app->jbstring->getId('color-');
$value = $this->app->data->create($this->getValue($this->identifier));


?>
<div id="<?php echo $containerId; ?>" class="jbzoo-color jbzoo">
    <?php echo $jbhtml->colors($type, $colorItems, $this->getName('color'), $value->get('color')); ?>
</div>



