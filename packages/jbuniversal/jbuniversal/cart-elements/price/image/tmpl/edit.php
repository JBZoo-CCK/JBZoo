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

$html = $this->app->jbhtml;
$value  = $this->getValue('value');
$unique = $this->app->jbstring->getId('image-');
?>

<div class="jbprice-img-row-file" id="<?php echo $unique; ?>">
    <?php
    echo $html->text($this->getControlName('value'), $value, 'class="jsJBPriceImage row-file" placeholder="Image"');
    ?>
</div>

<script type="text/javascript">
    (function($) {
        $('#<?php echo $unique; ?>').initJBPriceAdvImage();
    })(jQuery)
</script>