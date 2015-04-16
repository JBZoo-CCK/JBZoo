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


?>
<div class="jbfilter-cols">

    <div class="jbfilter-col width50">
        <?php echo $this->renderPosition('left', array('style' => 'filter.block')); ?>
    </div>

    <div class="jbfilter-col width50">
        <?php echo $this->renderPosition('right', array('style' => 'filter.block')); ?>
    </div>

    <?php echo JBZOO_CLR; ?>
</div>
