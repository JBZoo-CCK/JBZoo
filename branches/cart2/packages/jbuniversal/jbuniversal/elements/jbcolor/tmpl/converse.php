<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Oganov Alexander <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="jbzoo-color jbzoo">
    <?php
    $attributes = array(
        'title' => $label,
        'class' => array(
            'jb-color',
            'dye-' . $color
        )
    );

    $divAttrs = $jbHtml->buildAttrs($attributes);

    echo '<div ' . $divAttrs . '>'.$label.'</div>';

    echo '<div class="clear clr"></div>';
    ?>
</div>