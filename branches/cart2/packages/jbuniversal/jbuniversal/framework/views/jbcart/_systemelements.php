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

// get vars
$systemElements = isset($systemElements) ? $systemElements : array();
$elementGroup = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;

?>

<?php if (!empty($systemElements)) : ?>

    <fieldset>

        <legend><?php echo JText::_('JBZOO_ADMIN_POSITIONS_SYSTEM_ELEMENTS'); ?></legend>

        <ul class="element-list jsElementList unassigned" data-position="unassigned">

            <?php
            foreach ($systemElements as $element) {

                echo $this->partial('element', array(
                    'element'      => $element,
                    'elementGroup' => $elementGroup,
                ));
            }
            ?>

        </ul>
    </fieldset>

<?php endif; ?>
