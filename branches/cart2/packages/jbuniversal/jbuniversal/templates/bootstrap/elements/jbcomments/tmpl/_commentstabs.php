<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$jbCommId = uniqid('tabs-');
?>

<div class="comments-tabs-container">
    <ul id="<?php echo $jbCommId; ?>" class="nav nav-tabs">
        <?php
        $i = 0;
        foreach ($jbcomments as $key => $value) {
            $key   = JString::strtolower($key);
            $class = ($i == 0) ? 'active' : '';

            if (!empty($value)) {
                echo '<li id="com-tab-' . $i . '" class="' . $class . '">' .
                        '<a href="#jbcomment-tab-' . $key . '" data-toggle="tab">' .
                            '<span class="jbcomments" id="' . $key . '"></span>' .
                            JText::_($key) .
                        '</a>' .
                    '</li>';
            }

            $i++;
        }
        ?>
    </ul>

    <div id="<?php echo $jbCommId; ?>Content" class="tab-content">
        <?php
        $j = 0;
        foreach ($jbcomments as $key => $value) {
            $key     = JString::strtolower($key);
            $classes = 'tab-pane fade';

            if ($j == 0) {
                $classes .= ' in active';
            }

            if (!empty($value)) {
                echo '<div id="jbcomment-tab-' . $key . '" class="' . $classes . '">' . $value . '</div>';
            }

            $j++;
        }
        ?>
    </div>
</div>