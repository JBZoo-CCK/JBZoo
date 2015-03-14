<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// get vars
$elementGroup  = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;
$elementParams = isset($elementParams) ? $elementParams : null;
$positionKey   = isset($positionKey) ? $positionKey : JBCart::DEFAULT_POSITION;

// get elements meta data
$form = $element->getConfigForm($elementGroup);
$name = JText::_($element->config->get('name', 'JBZOO_ADMIN_ELEMENT_NEW'));

// uniqid vs radio input "checked" bug
$varName = 'tmp[' . $positionKey . '][' . $this->app->jbstring->getId($element->identifier . '--') . ']';

?>

<li class="element hideconfig jsElement" data-element="<?php echo $element->identifier; ?>">

    <div class="element-icon edit-element jsEdit" title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_EDIT'); ?>"></div>

    <div class="element-icon delete-element jsDelete"
         title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_DELETE'); ?>"></div>

    <div class="name jsSort" title="<?php echo JText::_('JBZOO_ADMIN_ELEMENT_SORT'); ?>">
        <?php echo $name; ?>
        <span>
            <i><?php
                echo $element->getMetaData('name')
                    . ($element->isCore() ? ' <em>(' . JText::_('JBZOO_ELEMENT_CORE') . ')</em>' : '')
                ?></i>
        </span>
    </div>

    <div class="config jsConfig">

        <?php

        // set additional params
        if ($elementParams) {
            $form->setValues($elementParams);
        }

        // render form HTML
        echo $form->render($varName, $elementGroup); ?>

        <input type="hidden" name="<?php echo $varName; ?>[type]" value="<?php echo $element->getElementType(); ?>"
               class="jsElementType" />

        <input type="hidden" name="<?php echo $varName; ?>[group]" value="<?php echo $element->getElementGroup(); ?>"
               class="jsElementGroup" />

        <input type="hidden" name="<?php echo $varName; ?>[identifier]" value="<?php echo $element->identifier; ?>"
               class="jsElementId" />

        <?php if ($element->getElementGroup() == JBCart::ELEMENT_TYPE_PRICE) : ?>
            <input type="hidden" name="<?php echo $varName; ?>[system]"
                   value="<?php echo (int)$element->isSystemTmpl(); ?>" class="jsElementCustomizable" />
        <?php endif; ?>

    </div>

</li>
