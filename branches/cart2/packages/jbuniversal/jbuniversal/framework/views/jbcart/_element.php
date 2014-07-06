<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// get elements meta data
$form = $element->getConfigForm();
$name = $element->config->get('name', 'New');
$var = 'elements[' . $element->identifier . ']';

?>

<li class="element hideconfig jsElement" data-element="<?php echo $element->identifier; ?>">

    <div class="element-icon edit-element jsEdit" title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_EDIT'); ?>"></div>

    <div class="element-icon delete-element jsDelete" title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_DELETE'); ?>"></div>

    <div class="name jsSort" title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_SORT'); ?>">
        <?php echo $name; ?>
        <span>(<?php echo $element->getMetaData('name'); ?>)</span>
    </div>

    <div class="config">
        <?php echo $form->render($var); ?>
        <input type="hidden" name="<?php echo $var; ?>[type]" value="<?php echo $element->getElementType(); ?>" class="jsElementType" />
        <input type="hidden" name="<?php echo $var; ?>[group]" value="<?php echo $element->getElementGroup(); ?>" class="jsElementGroup" />
        <input type="hidden" name="<?php echo $var; ?>[identifier]" value="<?php echo $element->identifier; ?>" class="jsElementId" />
    </div>

</li>
