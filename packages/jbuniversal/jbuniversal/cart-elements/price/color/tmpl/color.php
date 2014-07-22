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

$containerId = $this->app->jbstring->getId('color-');
$value = $this->app->data->create($this->getValue($this->identifier));
$attributes = ' width:' . $width . 'px; height:' . $height . 'px;';

?>

<div class="jbprice-param-radio jbprice-param-list jbpriceParams" data-index="0" data-type="<?php echo $type; ?>">
    <?php echo $this->app->jbhtml->colors($type, $colorItems, $this->getName('color'), null, $attributes); ?>
</div>



