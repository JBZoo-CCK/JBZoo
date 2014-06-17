<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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

<div id="<?php echo $galleryId; ?>">

    <?php foreach ($thumbs as $thumb) { ?>

        <a href="<?php echo $thumb['img']; ?>"
           rel="<?php echo $rel; ?>"
           title="<?php echo $thumb['name']; ?>"
           class="jbgallery"><img src="<?php echo $thumb['thumb']; ?>"
                                  alt="<?php echo $thumb['name']; ?>"
                                  title="<?php echo $thumb['name']; ?>"
                                  width="<?php echo $thumb['thumb_width']; ?>"
                                  height="<?php echo $thumb['thumb_height']; ?>"
                /></a>

    <?php } ?>

    <div class="clear clr"></div>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('#<?php echo $galleryId; ?> .jbgallery').fancybox({
            helpers: {
                "title"  : { type: "outside" },
                "buttons": { position: "top" },
                "thumbs" : { width: 80, height: 80 },
                "overlay": { locked: false}
            }
        });
    });
</script>
