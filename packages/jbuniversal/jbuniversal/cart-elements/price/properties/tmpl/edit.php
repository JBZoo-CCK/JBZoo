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

?>

<div class="jbprice-properties">
    <?php
    $html = array(
        $this->_jbhtml->text($this->getControlName('width'), $width, array(
            'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_WIDTH')
        )),
        $this->_jbhtml->text($this->getControlName('height'), $height, array(
            'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_HEIGHT')
        )),
        $this->_jbhtml->text($this->getControlName('length'), $length, array(
            'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_LENGTH')
        ))
    );

    echo implode('&nbsp;X&nbsp;', $html);
    ?>
</div>
