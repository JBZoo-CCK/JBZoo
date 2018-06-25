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
