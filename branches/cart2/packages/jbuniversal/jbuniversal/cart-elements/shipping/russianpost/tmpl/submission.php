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

$jbhtml = $this->app->jbhtml;


$uiqueId = $this->app->jbstring->getId('emspost-');
?>


<div id="<?php echo $uiqueId; ?>">
    <!--
    <div class="russianpost-viewPost">
        <?php echo $jbhtml->select(
            $this->_getViewPostList(),
            $this->getControlName('viewPost'),
            'jsViewPost',
            $this->get('viewPost', 23)
        ); ?>
    </div>
    -->

    <div class="russianpost-typePost">
        <?php echo $jbhtml->select(
            $this->_getTypePostList(),
            $this->getControlName('typePost'),
            'jsTypePost',
            $this->get('typePost', 1)
        ); ?>
    </div>

    <div class="russianpost-postOfficeId">
        <?php echo $jbhtml->text(
            $this->getControlName('postOfficeId'),
            $this->get('postOfficeId'), array(
                'placeholder' => JText::_('JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_ZIP'),
            )
        ); ?>
    </div>
</div>

<?php echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZooShippingTypeRussianPost', array(), true); ?>
