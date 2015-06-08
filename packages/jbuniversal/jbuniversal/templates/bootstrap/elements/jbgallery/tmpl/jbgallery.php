<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->fancybox();
?>

<div id="<?php echo $galleryId; ?>" class="gallery-container clearfix">

    <?php foreach ($thumbs as $thumb) { ?>
        <a href="<?php echo $thumb['img']; ?>" rel="<?php echo $rel; ?>" title="<?php echo $thumb['name']; ?>"
           class="jbgallery thumbnail block-divider">

            <img src="<?php echo $thumb['thumb']; ?>"
                 alt="<?php echo $thumb['name']; ?>"
                 title="<?php echo $thumb['name']; ?>"
                 width="<?php echo $thumb['thumb_width']; ?>"
                 height="<?php echo $thumb['thumb_height']; ?>" />

            <div class="uk-overlay-area"></div>
        </a>
    <?php } ?>
</div>

<?php echo $this->app->jbassets->widget('#' . $galleryId . ' .jbgallery', 'fancybox', array(
    'helpers' => array(
        'title'   => array('type' => 'outside'),
        'buttons' => array('position' => "top"),
        'thumbs'  => array('width' => 80, 'height' => 80),
        'overlay' => array('locked' => false)
    )
), true); ?>
