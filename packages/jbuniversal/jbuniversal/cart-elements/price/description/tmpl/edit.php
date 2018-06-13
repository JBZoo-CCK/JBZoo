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

$unique = $this->htmlId(true);
$attrs  = $this->_jbhtml->buildAttrs(array(
    'rows'        => '5',
    'style'       => 'resize: vertical;',
    'class'       => 'jsField description',
    'name'        => $this->getControlName('value'),
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_DESCRIPTION_PLACEHOLDER')
));

echo '<textarea ' . $attrs . '>' . $value . '</textarea>';

$this->app->jbassets->widget('.jsDescription .jsField', 'JBZoo.PriceEditElement_descriptionEdit');
