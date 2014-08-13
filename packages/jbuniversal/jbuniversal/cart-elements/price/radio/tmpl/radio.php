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

$attributes = array(
    'data-identifier' => $this->identifier
);

?>

<div class="jbprice-param-radio jbprice-param-list jbpriceParams"  data-type="radio">
    <?php echo $this->app->jbhtml->radio($options, $this->getName(), $attributes, $this->getBasic($this->identifier)); ?>
</div>