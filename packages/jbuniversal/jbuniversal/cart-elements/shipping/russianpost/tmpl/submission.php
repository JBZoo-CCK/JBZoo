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

$jbhtml = $this->app->jbhtml;


$uiqueId = $this->app->jbstring->getId('emspost-');
?>


<div id="<?php echo $uiqueId; ?>">
    <!--
    <div class="russianpost-viewPost">
        <?php echo $jbhtml->select(
            $this->_getViewPostList(),
            $this->getControlName('viewPost'),
            'jsViewPost',
            $this->get('viewPost', 23)
        ); ?>
    </div>
    -->

    <div class="russianpost-typePost">
        <?php echo $jbhtml->select(
            $this->_getTypePostList(),
            $this->getControlName('typePost'),
            'jsTypePost',
            $this->get('typePost', 1)
        ); ?>
    </div>

    <div class="russianpost-postOfficeId">
        <?php echo $jbhtml->text(
            $this->getControlName('postOfficeId'),
            $this->get('postOfficeId'), array(
                'placeholder' => JText::_('JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_ZIP'),
            )
        ); ?>
    </div>
</div>

<?php echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZooShippingTypeRussianPost', array(), true); ?>
