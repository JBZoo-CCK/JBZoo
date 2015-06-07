<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Kalistratov Sergey <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<style type="text/css">
    @media (max-width: 767px) {

        .jbclientarea-orderlist .jbclientarea-date:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_DATE'); ?>";
        }

        .jbclientarea-orderlist .jbclientarea-price:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_PRICE'); ?>";
        }

        .jbclientarea-orderlist .jbclientarea-status:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_STATUS'); ?>";
        }

    }
</style>