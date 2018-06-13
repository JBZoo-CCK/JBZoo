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

$this->app->jbdebug->mark('layout::alphaindex::start');

$alpha = $vars['object']->render();

if ($this->app->string->strpos($alpha, '#</span>')) {

    $alpha_en_ru = explode('#</span>', $alpha);
    $alpha_en_ru[0] .= '#</span>';

} elseif ($this->app->string->strpos($alpha, '#</a>')) {

    $alpha_en_ru = explode('#</a>', $alpha);
    $alpha_en_ru[0] .= '#</a>';
}

$alpha_chars = $vars['params']->get('config.alpha_chars', 0);
?>
    <div class="alphaindex uk-panel uk-panel-box uk-article-divider">
        <?php if ($alpha_chars == 0 || $alpha_chars == 2) { ?>
            <div class="alphaindex_line_en"><?php echo $alpha_en_ru[0]; ?></div>
        <?php } ?>

        <?php if ($alpha_chars == 0 || $alpha_chars == 1) { ?>
            <div class="alphaindex_line_ru"><?php echo $alpha_en_ru[1]; ?></div>
        <?php } ?>
    </div>
<?php
$this->app->jbdebug->mark('layout::alphaindex::finish');
