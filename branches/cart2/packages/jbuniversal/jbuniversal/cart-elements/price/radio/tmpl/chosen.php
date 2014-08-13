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

$unique = $this->app->jbstring->getId('select-chosen-');
$attributes = array(
    'class'           => 'jsParam',
    'data-identifier' => $this->identifier
);

if (count($options) > 0) :
    ?>

    <div class="jbprice-param-select jbprice-param-list jbpriceParams" data-type="select">
        <?php echo $this->app->jbhtml->selectChosen($options, $this->getName(), $attributes, $this->getBasic($this->identifier), $unique); ?>
    </div>

<?php endif;