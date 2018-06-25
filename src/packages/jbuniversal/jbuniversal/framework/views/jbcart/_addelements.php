<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
                    <?php
                    echo $this->app->jbstring->_('JBZOO_ADMIN_POSITIONS_LIST_' . $groupKey, JText::_('JBZOO_ADMIN_POSITIONS_LIST'));
                    ?>
                </div>

                <ul class="elements jsElementsGroup" data-group="<?php echo $groupKey; ?>">

                    <?php foreach ($groupItems as $itemKey => $element) {
                        $element->loadConfigAssets();

                        $attrs = array(
                            'data-type' => $itemKey,
                            'class'     => array('jsAddNewElement'),
                        );

                        if ($description = $element->getMetaData('description')) {
                            $attrs['class'][] = 'hasTip';
                            $attrs['title']   = $description;
                        }

                        echo '<li ' . $this->app->jbhtml->buildAttrs($attrs) . '>'
                            . $element->getMetaData('name')
                            . ' <span class="element-type">(' . $element->getElementType() . ')</span>'
                            . ($element->isCore() ? ' <em>(' . JText::_('JBZOO_ELEMENT_CORE') . ')</em>' : '')
                            . '</li>'
                            . PHP_EOL;
                    }
                    ?>
                </ul>

            <?php endforeach; ?>
        </div>

    </fieldset>
<?php endif; ?>