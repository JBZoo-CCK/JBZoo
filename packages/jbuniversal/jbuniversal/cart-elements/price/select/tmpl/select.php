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

$attributes = array(
    'class'           => 'jsParam',
    'data-identifier' => $this->identifier
);

if (count($data)) : ?>

    <div class="jbprice-param-select jbprice-param-list">
        <?php echo $this->app->jbhtml->select($data, $this->getRenderName('value'), $attributes,
            $this->getValue('value')); ?>
    </div>

<?php endif;
