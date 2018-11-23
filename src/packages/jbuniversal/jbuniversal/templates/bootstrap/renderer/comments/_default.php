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

$this->app->jbdebug->mark('layout::comments::start');

// css classes
$css[] = $vars['params']->get('max_depth', 5) > 1 ? 'nested' : null;
$css[] = $vars['params']->get('registered_users_only') && $vars['active_author']->isGuest() ? 'no-response' : null;

// add js and css
$this->app->document->addScript('libraries:jquery/plugins/cookie/jquery.cookie.js');
$this->app->document->addScript('assets:js/comment.js');

?>
    <div id="comments" class="comments <?php echo implode(" ", $css); ?> clearfix">

        <h3 class="comments-meta">
            <span
                class="comments-count"><?php echo JText::_('Comments') . ' (' . (count($vars['comments']) - 1) . ')'; ?></span>
        </h3>

        <?php

        if (!$this->app->jbcache->start(array(count($vars['comments']), $vars['params']), 'comments')) {
            ?>
            <ul class="level1 comment-list">
                <?php
                foreach ($vars['comments'][0]->getChildren() as $comment) {
                    echo $this->app->jblayout->render('comment', $vars['item'], array(
                            'level'   => 1,
                            'comment' => $comment,
                            'author'  => $comment->getAuthor(),
                            'params'  => $vars['params'],
                        )
                    );
                }
                ?>
            </ul>
            <?php
            $this->app->jbcache->stop();
        }

        if ($vars['item']->isCommentsEnabled()) {
            echo $this->app->jblayout->render('respond', $vars['item'], array(
                    'active_author' => $vars['active_author'],
                    'params'        => $vars['params'],
                    'item'          => $vars['item'],
                    'captcha'       => $vars['captcha'],
                )
            );
        }

        if ($vars['item']->canManageComments()) {
            echo $this->app->jblayout->render('comment_edit');
        }
        ?>

    </div>

<?php echo $this->app->jbassets->widget('#comments', 'Comment', array(
    'cookiePrefix'   => CommentHelper::COOKIE_PREFIX,
    'cookieLifetime' => CommentHelper::COOKIE_LIFETIME,
    'msgCancel'      => JText::_('Cancel'),
), true); ?>

<?php
$this->app->jbdebug->mark('layout::comments::finish');