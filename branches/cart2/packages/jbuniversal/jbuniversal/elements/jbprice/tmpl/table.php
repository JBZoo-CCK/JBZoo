<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$class  = 'jsJBPrice-' . $this->identifier . '-' . $this->getItem()->id;
$unique = $this->app->jbstring->getId('jbprice-'); ?>
<tr class="jsPrice jsJBPrice jbprice <?php echo $class . ' ' . $hash; ?>" id="<?php echo $unique; ?>">
    <?php echo $data,

    $this->app->jbassets->widget('#' . $unique, 'JBZoo.Price', array(
        'elements'   => $elements,
        'itemId'     => $this->getItem()->id,
        'identifier' => $this->identifier,
        'variantUrl' => $variantUrl,
    ), true); ?>
</tr>
