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

$attrs = array(
    'type'    => 'text',
    'name'    => $this->getControlName('value'),
    'id'      => $this->htmlId(true)
);

?>
    <input <?php echo $this->app->jbhtml->buildAttrs($attrs); ?> />

<?php if ($description = $this->config->get('description')) : ?>
    <p class="shippingfileds-description"> <?php echo $description; ?> </p>
<?php endif;