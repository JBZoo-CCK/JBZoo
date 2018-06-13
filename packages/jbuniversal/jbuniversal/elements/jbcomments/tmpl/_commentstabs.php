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

<?php echo $this->app->jbassets->widget('#' . $jbCommId, 'JBZoo.Tabs', array(), true); ?>
