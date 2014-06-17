<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$alias = $this->app->zoo->getApplication()->alias;

$jsScriptName = (defined('JDEBUG') && JDEBUG) ? 'jquery.jbzootools.orig.js' : 'jquery.jbzootools.min.js';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title><?php echo JText::_('JBZOO_CART_ADD_TO_CART'); ?></title>
    <link rel="stylesheet" href="<?php echo $this->app->path->url('jbassets:css/jbzoo.css'); ?>" type="text/css"/>

    <?php if ($this->app->path->path('jbassets:css/jbzoo.' . $alias . '.css')) : ?>
        <link rel="stylesheet" href="<?php echo $this->app->path->url('jbassets:css/jbzoo.' . $alias . '.css'); ?>"
              type="text/css"/>
    <?php endif; ?>

    <script src="<?php echo $this->app->path->url('libraries:jquery/jquery.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo $this->app->path->url('jbassets:js/' . $jsScriptName); ?>" type="text/javascript"></script>

</head>
<body class="jbcart-modal-body">
<div class="jbzoo jsCartModal">
    <?php echo $complexRender; ?>
    <div class="clear"></div>
</div>
</body>
</html>
