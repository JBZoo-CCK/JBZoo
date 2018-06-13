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
