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


// get element from parent parameter form
$element = $parent->element;
$config  = $element->config;

// init vars
$id = uniqid('option-');
$i  = 0;

?>

<div id="<?php echo $id; ?>" class="options">
    <ul>
        <?php
        foreach ($config->get('option', array()) as $opt) {
            if ($opt['name'] != '' || $opt['value'] != '') {
                echo '<li>' . $element->editOption($control_name, $i++, $opt['name'], $opt['value']) . '</li>';
            }
        }
        ?>
        <li class="hidden"><?php echo $element->editOption($control_name, '0', '', ''); ?></li>
    </ul>
    <div class="add"><?php echo JText::_('Add Option'); ?></div>
</div>

<?php echo $this->app->jbassets->widget('#' . $id, 'JBZooElementSelect', array(
    'variable' => $control_name,
    'url'      => $this->app->link(array(
        'controller' => 'manager',
        'format'     => 'raw',
        'task'       => 'getalias',
        'force_safe' => 1
    ), false)
), true); ?>
