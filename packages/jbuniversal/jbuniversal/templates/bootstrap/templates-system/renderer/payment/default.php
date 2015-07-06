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


$bootstrap = $this->app->jbbootstrap;
$rowClass  = $bootstrap->getRowClass();

if ($this->checkPosition('list')) : ?>
    <div class="jbcart-payment clearfix">
        <p class="jbcart-title"><?php echo JText::_('JBZOO_CART_PAYPMENT_TITLE'); ?></p>
        <?php
        echo $this->renderPosition('list', array(
            'style' => 'order.payment',
            'rowAttrs' => array(
                'class' =>  array(
                    $rowClass,
                ),
            ),
            'column' => 3
        ));
        ?>
    </div>
<?php endif;
