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


$jbCommId = uniqid('comments-tabs-');
?>

<div class="comments-tabs-container uk-panel uk-panel-box uk-panel-box-secondary">
    <ul class="uk-tab" data-uk-tab="{connect:'#<?php echo $jbCommId; ?>'}">
        <?php
        foreach ($jbcomments as $key => $value) {
            $key = JString::strtolower($key);

            if (!empty($value)) {
                echo '<li>' .
                        '<a href="#jbcomment-tab-' . $key . '">' .
                            '<span class="jbcomments" id="' . $key . '"></span>' .
                            JText::_($key) .
                        '</a>' .
                    '</li>';
            }
        }
        ?>
    </ul>

    <ul id="<?php echo $jbCommId; ?>" class="uk-switcher uk-margin">
        <?php
        foreach ($jbcomments as $key => $value) {
            $key = JString::strtolower($key);
            if (!empty($value)) {
                echo '<li id="jbcomment-tab-' . $key . '">' . $value . '</li>';
            }
        }
        ?>
    </ul>
</div>