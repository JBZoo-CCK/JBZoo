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
$variant = (int)$params->get('basic', 0) ? '' : '-variant';
$attr    = array(
    'class'       => 'discount' . $variant . '-input',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => 'Sku'
);
$variant = $this->config->get('variant', 0);

?>

<div class="sku<?php echo $variant; ?>">
    <?php echo $jbhtml->text($this->getName('_quantity'), $this->getValue('_quantity', 0), $attr); ?>
    <?php echo $this->getPosition(); ?>
</div>


