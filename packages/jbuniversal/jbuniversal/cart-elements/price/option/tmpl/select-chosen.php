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

$this->js('jbassets:js/libs/chosen.min.js');
$this->css('jbassets:css/libs/chosen.css'); ?>

<span class="jbprice-option-select-chosen">
    <?php echo $this->_jbhtml->selectChosen($data, $this->getRenderName('value'), null, $this->getValue(), $this->htmlId(true)); ?>
</span>
