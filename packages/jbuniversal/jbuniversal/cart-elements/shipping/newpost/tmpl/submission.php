<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$labelAttrs = array(
    'class' => 'field-label',
    'for'   => 'shipping-' . $this->identifier
);

$attrs = array(
    'type'    => 'radio',
    'name'    => $this->getControlName('_shipping_id'),
    'id'      => 'shipping-' . $this->identifier,
    'value'   => $this->identifier,
    'checked' => $this->isDefault() ? 'checked="checked"' : '',
    'class'   => 'jsInputShippingService shipping-service'
);

?>

    <input <?php echo $this->app->jbhtml->buildAttrs($attrs); ?> />

    <label <?php echo $this->app->jbhtml->buildAttrs($labelAttrs); ?>>

        <div class="jbradio"></div>

        <div class="shipping-info">

            <span class="name">
                <?php echo $this->getName(); ?>
            </span>

            <span class="value">
                (<?php echo JText::_('JBZOO_ELEMENT_SHIPPING_CHOOSE_PARAMS'); ?>)
            </span>
            
        </div>

    </label>

    <div class="more-options jsMoreOptions jsCalculate">
        <?php echo $this->renderFields(); ?>
    </div>

<?php if ($description = $this->config->get('description')) : ?>
    <p class="shipping-description"> <?php echo $description; ?> </p>
<?php endif;