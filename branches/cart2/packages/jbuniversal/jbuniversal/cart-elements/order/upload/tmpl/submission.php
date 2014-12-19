<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="<?php echo $this->identifier; ?>">

    <div class="download-select">

        <?php if ($max_size) { ?>
            <div class="info"><?php echo JText::sprintf( 'JBZOO_CART_UPLOAD_MAX_SIZE', $max_size ); ?></div>
        <?php } ?>

        <div class="upload">
            <input type="text" id="filename<?php echo $this->identifier; ?>" readonly="readonly"/>

            <div class="button-container">
                <input type="file" name="elements_<?php echo $this->identifier; ?>"
                       onchange="javascript: document.getElementById('filename<?php echo $this->identifier; ?>').value = this.value.replace(/^.*[\/\\]/g, '');"/>
            </div>
        </div>

        <input type="hidden" class="upload" name="<?php echo $this->getControlName('upload'); ?>"
               value="<?php echo $upload ? 1 : ''; ?>"/>

    </div>

</div>

<?php if ($max_size) { ?>
    <script>
        jQuery('input[name="elements_<?php echo $this->identifier; ?>"]').bind('change', function() {

            if (this.files[0].size > parseInt(<?php echo $max_size; ?>)) {

                alert("<?php echo JText::sprintf( 'JBZOO_CART_UPLOAD_MAX_SIZE_REACHED', $max_size ); ?>");
                jQuery(this).val('');
                jQuery('#filename<?php echo $this->identifier; ?>').val('');
                jQuery('input[name="<?php echo $this->getControlName('upload'); ?>"]').val('');

            }

        });
    </script>
<?php } ?>