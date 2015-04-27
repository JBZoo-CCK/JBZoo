<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$interface   = $this->_interfaceParams($params);
$inCartClass = $interface['isInCart'] ? 'in-cart' : 'not-in-cart';

$vars = array(
    'interface' => $interface,
    'params'    => $params,
);
?>

<!--noindex-->
<div class="jbprice-buttons jsPriceButtons <?php echo $inCartClass; ?>">

    <?php
    if ($params->get('add_show', 1)) {
        echo $this->_partial('add', $vars);
    }

    if ($params->get('oneclick_show', 0)) {
        echo $this->_partial('oneclick', $vars);
    }

    if ($params->get('modal_show', 0) && !$this->_isModal()) {
        echo $this->_partial('modal', $vars);
    }

    if ($params->get('goto_show', 0)) {
        echo $this->_partial('goto', $vars);
    }

    if ($params->get('remove_show', 1)) {
        echo $this->_partial('remove', $vars);
    }
    ?>

</div>
<!--/noindex-->
