<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$uniqId = $this->app->jbstring->getId('upload');

?>

<div id="<?php echo $uniqId; ?>" class="download-select">

    <?php if ($maxSizeBytes > 0) { ?>
        <div class="info"><?php echo JText::sprintf('JBZOO_CART_UPLOAD_MAX_SIZE', $maxSizeFormated); ?></div>
    <?php } ?>

    <div class="upload">
        <input type="text" class="jsFilename" readonly="readonly" />

        <div class="button-container">
            <input type="file" name="<?php echo $this->_getUploadName(); ?>" class="jsInputUpload" />
        </div>
    </div>

    <input type="hidden" class="jsUploadFlag" name="<?php echo $this->getControlName('upload'); ?>"
           value="<?php echo $uploadFlag; ?>" />

</div>

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooOrderUpload', array(
    'text_size_reached' => JText::sprintf('JBZOO_CART_UPLOAD_MAX_SIZE_REACHED', $maxSizeFormated),
    'max_size'          => $maxSizeBytes,
), true); ?>
