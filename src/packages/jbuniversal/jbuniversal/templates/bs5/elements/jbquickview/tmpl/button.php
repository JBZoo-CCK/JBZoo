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

$this->app->jbassets->fancybox();

$bootstrap = $this->app->jbbootstrap;
$btnAttrs  = array_replace_recursive($quickView->btnAttrs, array(
    'class' => 'btn btn-default quickview jsQuickView'
));

echo $quickView->js;

echo '<!--noindex-->' .
        '<a ' . $this->app->jbhtml->buildAttrs($btnAttrs) . '>' .
            $bootstrap->icon('eye-open') . '&nbsp;' .
            $quickView->buttonText .
        '</a>' .
    '<!--/noindex-->';
