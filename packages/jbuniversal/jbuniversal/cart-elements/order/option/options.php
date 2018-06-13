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
