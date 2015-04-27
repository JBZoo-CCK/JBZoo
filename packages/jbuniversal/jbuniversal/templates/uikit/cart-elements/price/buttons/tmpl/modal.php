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
if (!$this->_isModal()) : ?>
    <span class="jsAddToCartModal uk-button uk-button-success jbprice-buttons-modal">
        <i class="uk-icon-picture-o"></i>
        <?php echo JText::_($params->get('modal_label', 'JBZOO_ELEMENT_PRICE_BUTTONS_MODAL_LABEL_DEFAULT')); ?>
</span>
<?php endif;