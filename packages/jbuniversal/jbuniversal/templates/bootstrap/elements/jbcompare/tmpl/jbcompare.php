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

$bootstrap = $this->app->jbbootstrap;
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
        <a rel="nofollow" href="<?php echo $compareUrl; ?>"
           data-toggle="tooltip" data-placement="top" class="btn btn-primary" title="<?php echo JText::_('JBZOO_COMPARE_ITEMS'); ?>">
            <?php echo $bootstrap->icon('list', array('type' => 'white')); ?>
            <?php echo JText::_('JBZOO_COMPARE'); ?>
        </a>
        <span class="btn btn-danger jsCompareToggle" title="<?php echo JText::_('JBZOO_COMPARE_REMOVE'); ?>">
            <?php echo $bootstrap->icon('trash', array('type' => 'white')); ?>
        </span>
    </div>
    <div class="jbcompare-unactive">
        <span class="btn btn-primary jsCompareToggle"
              data-toggle="tooltip" data-placement="top" title="<?php echo JText::_('JBZOO_COMPARE_ADD'); ?>">
            <?php echo $bootstrap->icon('list', array('type' => 'white')); ?>
        </span>
    </div>
</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooCompareButtons', array(
    'url_toggle' => $ajaxUrl,
), true); ?>
