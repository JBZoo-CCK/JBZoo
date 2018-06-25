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

$url        = $vars['link'];
$pagination = $vars['object'];

$this->app->jbdebug->mark('layout::pagination::start');

if (!$pagination->getShowAll()) : ?>
    <ul class="uk-pagination">
        <?php echo $this->app->jbuikit->paginate($pagination, $url); ?>
    </ul>
<?php endif;
$this->app->jbdebug->mark('layout::pagination::finish');