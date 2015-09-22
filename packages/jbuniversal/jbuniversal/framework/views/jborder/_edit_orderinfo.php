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


// add check admin position
$html   = $this->orderFieldRender->renderAdminEdit(array('order' => $order));
$isShow = JString::trim(strip_tags($html));

?>

<?php if ($isShow) : ?>
    <div class="uk-panel">
        <h2><?php echo JText::_('JBZOO_ORDER_USERINFO'); ?></h2>
        <dl class="uk-description-list-horizontal"><?php echo $html; ?></dl>
    </div>
<?php endif; ?>
