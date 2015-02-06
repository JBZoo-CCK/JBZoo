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
$unique = $this->htmlId();

$this->app->jbassets->widget(".$unique", 'JBZooDiscountCode', array(
    'url' => $this->getAjaxUrl('ajaxSetCode'),
));

?>
<div class="jbdiscountcode <?php echo $unique; ?>">

    <input type="text" value="<?php echo $this->get('code'); ?>" name="<?php echo $this->getControlName('code'); ?>"
           class="jsCode input-code" />

    <span class="jsSendCode jbbutton small"><?php echo JText::_('JBZOO_ELEMENT_DISCOUNTCODE_SEND'); ?></span>

    <div class="jsMoneyWrap"><?php echo $this->getRate()->html(); ?></div>
</div>
