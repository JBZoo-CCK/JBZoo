<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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
