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

if (!$this->_isModal()) : ?>
    <span class="jsAddToCartModal uk-button uk-button-success jbprice-buttons-modal">
        <i class="uk-icon-picture-o"></i>
        <?php echo JText::_($params->get('modal_label', 'JBZOO_ELEMENT_PRICE_BUTTONS_MODAL_LABEL_DEFAULT')); ?>
</span>
<?php endif;