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

$elId = $this->app->jbstring->getId('');
$name = $element->config->get('type');
$lang = JText::_($name);
$class = 'simple-param';

if ($element->isCore()) {
    $name  = strtoupper($params['type']);
    $lang  = JText::_('JBZOO_JBPRICE_VARIATION_' . $name);
    $class = 'core-param';
}

?>
<div class="variant-<?php echo strtolower($name); ?>-wrap <?php echo $class; ?> variant-param">
    <strong class="hasTip row-field label"
            title="<?php echo $lang; ?>">
        <?php echo JString::ucfirst($lang); ?>
    </strong>
    <span class="attention jsJBpriceAttention"></span>

    <div class="field">
        <?php echo $element->edit($params); ?>
    </div>

</div>
