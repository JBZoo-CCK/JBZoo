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
