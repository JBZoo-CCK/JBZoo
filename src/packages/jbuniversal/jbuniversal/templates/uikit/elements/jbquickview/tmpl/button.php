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
