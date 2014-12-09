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

?>
<!--noindex-->
<div class="jbcompare jsJBZooCompare <?php echo($isExists ? ' active ' : 'unactive'); ?>">

    <div class="jbcompare-active">
        <a rel="nofollow" href="<?php echo $ajaxUrl; ?>" class="jsCompareToggle jbbutton small"
           title="<?php echo JText::_('JBZOO_COMPARE_REMOVE'); ?>"><?php echo JText::_('JBZOO_COMPARE_REMOVE'); ?></a>

        <a rel="nofollow" href="<?php echo $compareUrl; ?>" class="jbbutton small"
           title="<?php echo JText::_('JBZOO_COMPARE'); ?>"><?php echo JText::_('JBZOO_COMPARE'); ?></a>
    </div>

    <div class="jbcompare-unactive">
        <a rel="nofollow" href="<?php echo $ajaxUrl; ?>" class="jsCompareToggle jbbutton small"
           title="<?php echo JText::_('JBZOO_COMPARE_ADD'); ?>"><?php echo JText::_('JBZOO_COMPARE_ADD'); ?></a>
    </div>

</div>
<!--/noindex-->
