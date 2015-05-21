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


$this->app->jbassets->compare();

$uniqId    = $this->app->jbstring->getId('compare-');
$wrapAttrs = array(
    'id'    => $uniqId,
    'class' => array(
        'jsJBZooCompare',
        'jbcompare-buttons',
        $isExists ? ' active ' : 'unactive'
    )
);

?>
<!--noindex-->
<div <?php echo $this->app->jbhtml->buildAttrs($wrapAttrs); ?>>

    <div class="jbcompare-active">
        <a rel="nofollow" href="<?php echo $compareUrl; ?>" class="jbbutton small">
            <?php echo JText::_('JBZOO_COMPARE'); ?>
        </a>

        <span class="jbbutton small jsCompareToggle"><?php echo JText::_('JBZOO_COMPARE_REMOVE'); ?></span>
    </div>

    <div class="jbcompare-unactive">
        <span class="jbbutton small jsCompareToggle"><?php echo JText::_('JBZOO_COMPARE_ADD'); ?></span>
    </div>

</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooCompareButtons', array(
    'url_toggle' => $ajaxUrl
), true); ?>
