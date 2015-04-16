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