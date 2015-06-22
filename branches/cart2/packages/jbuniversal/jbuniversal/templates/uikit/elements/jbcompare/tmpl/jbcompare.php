<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
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
        <a rel="nofollow" href="<?php echo $compareUrl; ?>" class="uk-button uk-button-success" title="<?php echo JText::_('JBZOO_COMPARE_ITEMS'); ?>">
            <i class="uk-icon-bar-chart"></i>
        </a>
        <span class="uk-button uk-button-danger jsCompareToggle" title="<?php echo JText::_('JBZOO_COMPARE_REMOVE'); ?>">
            <i class="uk-icon-trash"></i>
        </span>
    </div>
    <div class="jbcompare-unactive">
        <span class="uk-button uk-button-success jsCompareToggle" title="<?php echo JText::_('JBZOO_COMPARE_ADD'); ?>">
            <i class="uk-icon-bar-chart-o"></i>
        </span>
    </div>
</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooCompareButtons', array(
    'url_toggle' => $ajaxUrl,
), true); ?>
