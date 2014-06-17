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


$this->app->jbdebug->mark('layout::myorders::start');

$view = $this->getView();

if (count($vars['objects'])) : ?>
    <table class="jbbasket-table" border="1" cellpadding="3" cellspacing="3">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo JText::_('JBZOO_MYORDERS_NAME'); ?></th>
            <th><?php echo JText::_('JBZOO_MYORDERS_PRICE'); ?></th>
            <th><?php echo JText::_('JBZOO_MYORDERS_STATUS'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($vars['objects'] as $id => $item) {

            $layout = $this->app->jblayout->_getItemLayout($item, 'teaser');

            echo $view->renderer->render($layout, array(
                'view' => $view,
                'item' => $item
            ));
        }
        ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="color:#a00;"><?php echo JText::_('JBZOO_MYORDERS_EMPTY'); ?></p>
<?php endif;

$this->app->jbdebug->mark('layout::myorders::finish');
