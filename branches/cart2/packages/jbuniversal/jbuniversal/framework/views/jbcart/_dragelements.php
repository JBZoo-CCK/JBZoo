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
$dragElements = isset($dragElements) ? $dragElements : array();
$elementGroup = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;

?>

<?php if (!empty($dragElements)) : ?>

    <fieldset>

        <legend><?php echo JText::_('JBZOO_ADMIN_POSITIONS_AVAILABLE_ELEMENTS'); ?></legend>

        <ul class="element-list jsElementList unassigned" data-position="unassigned">

            <?php
            foreach ($dragElements as $element) {

                echo $this->partial('element', array(
                    'element'      => $element,
                    'elementGroup' => $elementGroup,
                ));
            }
            ?>

        </ul>
    </fieldset>

<?php endif; ?>
