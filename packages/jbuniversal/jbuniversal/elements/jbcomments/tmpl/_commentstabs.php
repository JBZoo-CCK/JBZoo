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

<div id="<?php echo $jbCommId; ?>">
    <ul>
        <?php
        foreach ($jbcomments as $key => $value) {
            $key = JString::strtolower($key);

            if (!empty($value)) {
                echo '<li>
                        <span class="jbcomments" id="' . $key . '"></span>
                        <a href="#jbcomment-tab-' . $key . '">' . JText::_($key) . '</a>
                      </li>';
            }
        }
        ?>
    </ul>

    <?php
    foreach ($jbcomments as $key => $value) {
        $key = JString::strtolower($key);
        if (!empty($value)) {
            echo '<div id="jbcomment-tab-' . $key . '">' . $value . '</div>';
        }
    }
    ?>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('#<?php echo $jbCommId ; ?>').JBZooTabs();
    });
</script>
