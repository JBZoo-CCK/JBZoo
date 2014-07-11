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


$groupList = isset($groupList) ? $groupList : array();

?>
<?php if (!empty($groupList)) : ?>
    <fieldset>

        <legend><?php echo JText::_('JBZOO_ADMIN_POSITIONS_LIBS'); ?></legend>

        <div class="groups">
            <?php foreach ($groupList as $groupKey => $groupItems) : ?>
                <div class="elements-group-name">
                    <?php echo JText::_('JBZOO_ADMIN_POSITIONS_' . $groupKey); ?>
                </div>

                <ul class="elements jsElementsGroup" data-group="<?php echo $groupKey; ?>">
                    <?php foreach ($groupItems as $itemKey => $element) :
                        $element->loadConfigAssets();
                        ?>
                        <li data-type="<?php echo $itemKey; ?>" class="jsAddNewElement">
                            <?php echo JText::_($element->getMetaData('name')); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php endforeach; ?>
        </div>

    </fieldset>
<?php endif; ?>