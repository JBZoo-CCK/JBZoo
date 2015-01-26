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

$class  = 'jsJBPrice-' . $this->identifier . '-' . $this->getItem()->id;
$unique = $this->app->jbstring->getId('jbprice-adv-'); ?>
<div class="jsPrice jsJBPrice jbprice <?php echo $class; ?>" id="<?php echo $unique; ?>">
    <?php echo $data; ?>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('#<?php echo $unique;?>').JBZooPrice({
            'elements': <?php echo json_encode($elements);?>,
            'itemId': <?php echo $this->getItem()->id;?>,
            'identifier': "<?php echo $this->identifier;?>",
            'variantUrl': "<?php echo $variantUrl; ?>"
        });
    });
</script>
