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


$this->app->jbdebug->mark('layout::frontpage::start');

// set vars
$category = $vars['object'];
$title    = $this->app->string->trim($vars['params']->get('content.category_title', ''));
$subTitle = $this->app->string->trim($vars['params']->get('content.category_subtitle', ''));
$image    = $this->app->jbimage->get('category_image', $vars['params']);
$title    = $title ? $title : $category->name;

if ((int)$vars['params']->get('template.category_show', 1)) : ?>
    <div class="frontpage clearfix alias-<?php echo $category->alias; ?> uk-grid">
        <div class="uk-width-medium-1-1">
            <div class="uk-panel uk-panel-box">
                <?php if ((int)$vars['params']->get('template.category_title_show', 1)) : ?>
                    <h1 class="title"><?php echo $title; ?></h1>
                <?php endif; ?>


                <?php if ((int)$vars['params']->get('template.category_subtitle', 1) && !empty($subTitle)) : ?>
                    <h2 class="subtitle"><?php echo $subTitle; ?></h2>
                <?php endif; ?>


                <?php if ((int)$vars['params']->get('template.category_image', 1) && $image['src']) : ?>
                    <div class="image-full uk-align-<?php echo $vars['params']->get('template.category_image_align', 'left'); ?>">
                        <img src="<?php echo $image['src']; ?>" <?php echo $image['width_height']; ?>
                             title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>" class="uk-thumbnail"/>
                    </div>
                <?php endif; ?>


                <?php if ((int)$vars['params']->get('template.category_teaser_text', 1) && $vars['params']->get('content.category_teaser_text', '')) : ?>
                    <div class="description-teaser">
                        <?php echo $vars['params']->get('content.category_teaser_text', ''); ?>
                    </div>
                <?php endif; ?>


                <?php if ((int)$vars['params']->get('template.category_text', 1) && $category->description) : ?>
                    <div class="description-full"><?php echo $category->getText($category->description); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>

    <div class="frontpage alias-<?php echo $category->alias; ?> uk-grid">
        <div class="uk-width-medium-1-1">
            <?php if ((int)$vars['params']->get('template.category_title_show', 1)) : ?>
                <h1 class="title"><?php echo $title; ?></h1>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php
$this->app->jbdebug->mark('layout::frontpage::finish');