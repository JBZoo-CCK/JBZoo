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

?>
<div class="clear" style="margin-top:36px;"></div>
<div class="jbfooter">
    <hr>

    <div style="float: right;">
        <a href="http://jbzoo.com" target="_blank" title="JBZoo.com">JBZoo CCK Project Page</a>
    </div>

    Joomla: <strong><?php echo $this->app->jbversion->joomla(); ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
    JBZoo: <strong><?php echo $this->app->jbversion->jbzoo(); ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
    Zoo: <strong><?php echo $this->app->jbversion->zoo(); ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;

    <?php if ($widgetKit = $this->app->jbversion->widgetkit()) :?>
        WidgetKit: <strong><?php echo $widgetKit; ?></strong>
    <?php endif; ?>
</div>