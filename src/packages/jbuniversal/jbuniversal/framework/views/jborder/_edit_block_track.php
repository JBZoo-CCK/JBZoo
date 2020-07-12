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

?>
<div class="uk-panel uk-panel-box">

    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_TRACK_TITLE'); ?></h3>

    <dl class="uk-description-list-horizontal">
        <dt><?php echo JText::_('JBZOO_ORDER_TRACK'); ?></dt>
        <dd><p><?php echo $this->app->jbhtml->text('order[track]', $order->track, '', ''); ?></p></dd>
    </dl>

</div>