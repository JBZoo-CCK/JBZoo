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

$jbstring = $this->app->jbstring;

?>
<fieldset>

    <legend><?php echo JText::_('JBZOO_ADMIN_POSITIONS_' . $this->task); ?></legend>

    <?php if (!empty($positions)) : ?>

        <?php foreach ($positions as $positionKey => $position) : ?>

            <div class="position">
                <?php

                $postFix = $this->task . '_' . str_replace('-', '_', $positionKey);

                // get position description
                $positionDesc = 'JBZOO_ADMIN_POSITION_' . $postFix . '_DESC';
                $positionDesc = $jbstring->_($positionDesc, JText::_('JBZOO_ADMIN_POSITION') . ': <em>' . $positionKey . '</em>');

                // get position name
                $positionName = 'JBZOO_ADMIN_POSITION_' . $postFix;
                if ($this->app->jbrequest->is('task', 'statusevents')) {

                    list($statusType, $statusCode) = explode('__', $positionKey);
                    $statusElement = $this->app->jbcartstatus->getByCode($statusCode, $statusType);
                    $positionName  = $jbstring->_($positionName, ($statusElement ? $statusElement->getName() : null));

                    $positionDesc = $statusElement ? $statusElement->getDescription() : '';
                    if (!$positionDesc) {
                        $positionDesc = JText::_('JBZOO_ADMIN_POSITION_EVENTCODE') . ': <em>' . $positionKey . '</em>';
                    }

                } else {
                    $positionName = $jbstring->_($positionName, $positionKey);
                }

                echo '<span class="position-name">' . $positionName . '</span>';
                echo ' <span class="position-desc">( ' . $positionDesc . ' )</span>';

                ?>
            </div>

            <ul class="element-list jsElementList" data-position="<?php echo $positionKey; ?>">

                <?php
                foreach ($position as $elementId => $element) {

                    $elementParams = isset($elementsParams[$positionKey][$elementId]) ? $elementsParams[$positionKey][$elementId] : array();

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

        <p class="positions-empty">
            <?php
            $emptyText = 'JBZOO_ADMIN_EVENT_POSITIONS_EMPTY_' . $this->task;
            echo $jbstring->_($emptyText, JText::_('JBZOO_ADMIN_EVENT_POSITIONS_EMPTY'));
            ?>
        </p>

    <?php endif; ?>

</fieldset>
