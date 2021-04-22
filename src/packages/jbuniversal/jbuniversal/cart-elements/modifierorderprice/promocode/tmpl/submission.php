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

$unique = $this->htmlId();

$this->app->jbassets->widget(".$unique", 'JBZooPromoCode', array(
    'url' => $this->getAjaxUrl('ajaxSetCode'),
));

?>
<div class="jbpromocode <?php echo $unique; ?>">

    <input type="text" value="<?php echo $this->get('code'); ?>" name="<?php echo $this->getControlName('code'); ?>"
           class="jsCode input-code" />

    <span class="jsSendCode jbbutton small"><?php echo JText::_('JBZOO_ELEMENT_DISCOUNTCODE_SEND'); ?></span>

    <div class="jsMoneyWrap"><?php echo $this->getRate()->html(); ?></div>
</div>
