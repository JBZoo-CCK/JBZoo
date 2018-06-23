<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
