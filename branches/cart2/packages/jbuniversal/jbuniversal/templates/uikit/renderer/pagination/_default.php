<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

$url        = $vars['link'];
$pagination = $vars['object'];

$this->app->jbdebug->mark('layout::pagination::start');

if (!$pagination->getShowAll()) : ?>
    <ul class="uk-pagination">
        <?php echo $this->app->jbuikit->paginate($pagination, $url); ?>
    </ul>
<?php endif;
$this->app->jbdebug->mark('layout::pagination::finish');