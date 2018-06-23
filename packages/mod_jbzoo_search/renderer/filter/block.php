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

$params = $this->app->data->create($params);

// create label
$label = '';
if ($params->get('showlabel', 1) && strpos($element->getElementType(), 'jbprice') !== 0) {

    $labelText = $params->get('altlabel') ? $params->get('altlabel') : $element->getConfig()->get('name');

    if ($params->get('jbzoo_filter_render') != 'jqueryui') {
        $label = '<label class="jbfilter-label" for="' . $attrs['id'] . '">' . $labelText . '</label>';
    } else {
        $label = '<label class="jbfilter-label">' . $labelText . '</label>';
    }
}


// create class attribute
$classes = array_filter([
    'jbfilter-row',
    'jbfilter-' . trim($params->get('jbzoo_filter_render', 'default'), '_'),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
]);


?>
<div class="<?php echo implode(' ', $classes); ?>">
    <?php
    echo $label;
    echo '<div class="jbfilter-element">' . $elementHTML . '</div>';
    echo JBZOO_CLR;
    ?>
</div>
