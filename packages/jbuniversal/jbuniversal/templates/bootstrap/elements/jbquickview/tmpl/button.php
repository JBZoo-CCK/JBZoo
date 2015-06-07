<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->jbassets->fancybox();

$btnAttrs = array_replace_recursive($quickView->btnAttrs, array(
    'class' => 'uk-button uk-button-small quickview jsQuickView'
));

echo $quickView->js;

echo '<!--noindex-->' .
        '<a ' . $this->app->jbhtml->buildAttrs($btnAttrs) . '>' .
            '<i class="uk-icon-eye"></i>&nbsp;' .
            $quickView->buttonText .
        '</a>' .
    '<!--/noindex-->';
