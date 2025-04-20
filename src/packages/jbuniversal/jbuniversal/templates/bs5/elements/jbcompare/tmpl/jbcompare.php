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
