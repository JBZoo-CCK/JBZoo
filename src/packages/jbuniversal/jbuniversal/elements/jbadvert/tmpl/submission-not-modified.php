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

$controlType = $params->get('control_type', 'button');
?>

<div class="jbadvert-wrapper">

    <div class="jbadvert-name">
        <?php if ($params->get('name_show', 1)) {
            echo $this->config->get('name') . ' &mdash; ';
        } ?>

        <?php if ($params->get('price_show', 1)) { ?>
            <span class="jbadvert-price"><?php echo $this->_getPrice()->html(); ?></span>
        <?php } ?>
    </div>

    <?php

    echo '<div class="jbadvert-' . $controlType . '">';
    if ($controlType == 'button') {
        echo implode(PHP_EOL, array(
            '<div class="jbadvert-button">',
            '<input ' . $this->app->jbhtml->buildAttrs(array(
                'type'  => 'submit',
                'class' => 'jbbutton',
                'name'  => 'jbadvert_gotocart',
                'value' => JText::_('JBZOO_JBADVERT_ADD_TO_CART'),
            )) . ' />',
            '</div>',
            $this->app->jbhtml->hidden($this->getControlName('gotocart'), 1),
        ));
    } else if ($controlType != 'hidden') {
        echo implode(PHP_EOL, array(
            '<div class="jbadvert-control">',
            '<div class="jbadvert-control-name">' . JText::_('JBZOO_JBADVERT_CONTROL_NAME') . '</div>',
            $this->app->jbhtml->bool($params->get('control_type', 'radio'), $this->getControlName('gotocart'), array(), 0),
            JBZOO_CLR,
            '</div>',
        ));
    }
    echo '</div>';

    echo $this->app->jbhtml->hidden($this->getControlName('zoohack'), 1);
    ?>

</div>
