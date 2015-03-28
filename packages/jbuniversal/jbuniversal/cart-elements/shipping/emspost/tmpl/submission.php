<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$uiqueId = $this->app->jbstring->getId('emspost-');

$locTypes = array('russia', 'countries', 'regions', 'cities');

?>

<div class="emspost-sender-city">
    <p>
        <strong><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_EMSPOST_SENDER'); ?>:</strong>
        <?php echo $this->_getDefaultCityName(); ?>
    </p>
</div>

<div id="<?php echo $uiqueId; ?>">
    <?php
    foreach ($locTypes as $type) {

        if ($this->config->get($type)) {

            $list = JBCartElementShippingEmsPost::getLocations($type);

            echo '<div class="emspost-' . $type . '">'
                . $this->app->jbhtml->select($list, $this->getControlName($type), array('class' => 'jsEms-' . $type), $this->get($type))
                . '</div>';
        }
    }
    ?>
</div>

<?php echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZooShippingTypeEms', array(
    'url_price' => $this->getAjaxUrl('ajaxGetPrice'),
    'text_free' => JText::_('JBZOO_FREE')
), true); ?>
