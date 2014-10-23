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

if ($this->checkPosition('fields')) : ?>
    <div class="jbzoo-basket-fields">
        <h2>Контактная информация</h2>

        <?php echo $this->renderPosition('fields', array(
            'style' => 'order.block'
        )); ?>
    </div>
<?php endif;
