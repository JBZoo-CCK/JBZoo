<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($fields)) : ?>
    <ul>
        <?php foreach ($fields as $field) : ?>
            <li>
                <strong>Желаемое время доставки</strong>
                <?php
                $date = new JDate($field);
                echo $date->calendar('D, d M Y H:i:s', false, true);
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif;