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

if (!$this->app->jbenv->isSite()) {
    $this->app->jbassets->widget('#' . $unique, 'JBZooMedia');
}
?>

<div class="jsMedia jbprice-img-row-file" id="<?php echo $unique; ?>">
    <?php
    echo $this->_jbhtml->text($this->getControlName('value'), $value, array(
        'class'       => 'jsJBPriceImage jsMediaValue row-file',
        'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_IMAGE_EDIT_PLACEHOLDER')
    )); ?>
</div>
