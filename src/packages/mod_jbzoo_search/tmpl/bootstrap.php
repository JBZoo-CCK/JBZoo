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

$modHelper->app->jbassets->css('jbassets:css/bootstrap.min.css');

$formAttrs = [
    'id'     => $modHelper->getModuleId(),
    'name'   => $modHelper->getModuleId(),
    'method' => 'get',
    'action' => JRoute::_('index.php?Itemid=' . $modHelper->getMenuId()),
    'class'  => [
        'jsFilter',
        'jbfilter',
        'jbfilter-' . $itemLayout,
    ],
];

?>

<div class="jbzoo jbfilter-wrapper">

    <form <?php echo $modHelper->attrs($formAttrs); ?>>
        <?php
        echo $modHelper->partial('_fields');

        echo '<div class="jbfilter-static">';
        echo $modHelper->partial('_pages');
        echo $modHelper->partial('_order');
        echo $modHelper->partial('_logic');
        echo '</div>';

        echo $modHelper->partial('_buttons', ['btnClass' => 'btn btn-default']);
        echo $modHelper->partial('_hidden');
        ?>
    </form>

</div>

