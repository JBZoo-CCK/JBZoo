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

$elementGroup   = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;
$elementsParams = isset($elementsParams) ? $elementsParams : array();

?>
<fieldset>

    <legend><?php echo JText::_('JBZOO_ADMIN_POSITIONS_' . $this->task); ?></legend>

    <?php if (!empty($positions)) : ?>

        <?php foreach ($positions as $positionKey => $position) : ?>

            <div class="position">
                <?php

                $positionName = 'JBZOO_ADMIN_EVENT_POSITION_' . $this->task . '_' . str_replace('-', '_', $positionKey);
                $positionDesc = 'JBZOO_ADMIN_EVENT_POSITION_' . $this->task . '_' . str_replace('-', '_', $positionKey) . '_DESC';

                $language = JFactory::getLanguage();
                if ($language->hasKey($positionName)) {
                    echo '<span class="position-name">' . JText::_($positionName) . '</span>';
                } else {
                    echo '<span class="position-name">' . $positionKey . '</span>';
                }

                if ($language->hasKey($positionName)) {
                    echo ' <span class="position-desc">(' . JText::_($positionName) . ')</span>';
                }

                ?>
            </div>

            <ul class="element-list jsElementList" data-position="<?php echo $positionKey; ?>">

                <?php
                foreach ($position as $elementId => $element) {

                    $elementParams = isset($elementsParams[$element->identifier]) ? $elementsParams[$element->identifier] : array();

                    echo $this->partial('element', array(
                        'element'       => $element,
                        'positionKey'   => $positionKey,
                        'elementGroup'  => $elementGroup,
                        'elementParams' => $elementParams,
                    ));
                }
                ?>

            </ul>

        <?php endforeach; ?>

    <?php else: ?>

        <p class="positions-empty"><?php echo JText::_('JBZOO_ADMIN_EVENT_POSITIONS_EMPTY'); ?></p>

    <?php endif; ?>

</fieldset>
