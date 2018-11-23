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

$user = JText::_('JBZOO_ANONYM');
if ($juser = $order->getAuthor()) {
    $href = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $juser->id));
    $user = '<a href="' . $href . '">' . $juser->name . '</a>';
}

/** @var JBCartStatusHelper $jbstatus */
$jbstatus   = $this->app->jbcartstatus;
$statusList = $jbstatus->getList(JBCart::STATUS_ORDER, true, true, $order);
$curStatus  = $order->getStatus()->getCode();

?>
<div class="uk-panel uk-panel-box basic-info">
    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_MAIN_TITLE'); ?></h3>

    <dl class="uk-description-list-horizontal">

        <dt><?php echo JText::_('JBZOO_ORDER_STATUS'); ?></dt>
        <dd><p><?php echo $this->app->jbhtml->select($statusList, 'order[status]', '', $curStatus); ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_NO'); ?></dt>
        <dd><p class="uk-badge uk-badge-notification"><?php echo $order->getName(); ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_USER'); ?></dt>
        <dd><p><?php echo $user; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_CREATED'); ?></dt>
        <dd><p><?php echo $created; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_MODIFIED'); ?></dt>
        <dd><p><?php echo $modified; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_NOTES'); ?></dt>
        <dd>
            <textarea cols="100" rows="5" style="resize: vertical;" name="order[comment]"
                      placeholder="<?php echo JText::_('JBZOO_ORDER_NOTES_DESC'); ?>"><?php
                echo $order->comment;
                ?></textarea>
        </dd>
    </dl>
</div>