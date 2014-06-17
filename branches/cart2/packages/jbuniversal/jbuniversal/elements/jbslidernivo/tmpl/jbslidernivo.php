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


$this->app->jbassets->nivoslider();

$sliderId = uniqid('nivoslider-');
if (in_array('random', $params['effects'])) {
    $params['effects'] = array('random');
}

$paramsData = $this->app->data->create($params);

?>

<div class="slider-wrapper theme-default">
    <div id="<?php echo $sliderId;?>" class="nivoSlider ">
        <?php foreach ($thumbs as $thumb) { ?>
        <img src="<?php echo $thumb['thumb']; ?>" data-thumb="<?php echo $thumb['thumb']; ?>" alt="" title="" />
        <?php } ?>
    </div>
    <div id="<?php echo $sliderId;?>" class="nivo-html-caption"></div>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $("#<?php echo $sliderId;?>").nivoSlider({
            effect          :"<?php echo implode(',', $params['effects']);?>",
            animSpeed       :<?php echo (int)$paramsData->get('animSpeed', 500);?>,
            pauseTime       :<?php echo (int)$paramsData->get('pauseTime', 3000);?>,
            randomStart     :<?php echo (int)$paramsData->get('randomStart', 0);?>,
            controlNavThumbs:<?php echo (int)$paramsData->get('controlNavThumbs', 0);?>,
            manualAdvance   :<?php echo (int)$paramsData->get('manualAdvance', 0);?>,
            // others options - defaults values
            prevText        :'<?php echo JText::_('JBZOO_PREV');?>',
            nextText        :'<?php echo JText::_('JBZOO_NEXT');?>',
            slices          :15,
            boxCols         :8,
            boxRows         :4,
            startSlide      :0,
            directionNav    :true,
            directionNavHide:true,
            controlNav      :true,
            pauseOnHover    :true,
            beforeChange    :new Function(),
            afterChange     :new Function(),
            slideshowEnd    :new Function(),
            lastSlide       :new Function(),
            afterLoad       :new Function()
        });
    });
</script>
