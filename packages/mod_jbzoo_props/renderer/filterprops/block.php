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

if (!empty($elementHTML)) {

    // create label
    $label = '';
    if (isset($params['showlabel']) && $params['showlabel']) {
        $label .= '<div class="label">';
        $label .= ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
        $label .= '</div>';
    }

    // create class attribute
    $classes = array_filter(array(
        'props-element',
        'clearfix',
        ($params['first']) ? 'first' : '',
        ($params['last']) ? 'last' : '',
    ));

    ?>
    <div class="<?php echo implode(' ', $classes); ?>">
        <?php echo $label . '<div class="field">' . $elementHTML . '</div>'; ?>
    </div>
<?php
}
