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

// create label
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
    $label .= '<strong>';
    $label .= ($params['altlabel']) ? $params['altlabel'] : $element->config->get('name');
    $label .= '</strong>: ';
}

// create class attribute
$classes = array_filter(array(
    'element',
    'element-' . $element->getElementType(),
    $params['first'] ? ' first' : '',
    $params['last'] ? ' last' : ''
));

?>
<li class="<?php echo implode(' ', $classes); ?>">
    <?php echo $label . $element->render($params); ?>
</li>