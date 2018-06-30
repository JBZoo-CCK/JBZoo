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