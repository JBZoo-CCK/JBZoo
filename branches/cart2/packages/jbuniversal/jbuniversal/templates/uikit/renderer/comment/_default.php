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


// profiler
$this->app->jbdebug->mark('layout::comment(' . $vars['comment']->id . ')::start');

// set vars
$comment       =& $vars['comment'];
$author        =& $vars['author'];
$params        =& $vars['params'];
$level         =& $level['params'];
$childComments = $comment->getChildren();

// set author name
$vars['author']->name = $vars['author']->name ? $vars['author']->name : JText::_('Anonymous');

?>
    <li>
        <div id="comment-<?php echo $comment->id; ?>" class="uk-comment comment <?php if ($author->isJoomlaAdmin()) {
            echo 'comment-byadmin';
        } ?>">

            <div class="comment-head uk-comment-header">
                <!--noindex-->
                <?php if ($params->get('avatar', 0)) : ?>
                    <div class="avatar uk-comment-avatar"><?php echo $author->getAvatar(50); ?></div>
                <?php endif; ?>

                <?php if ($author->url) : ?>
                    <h3 class="author uk-comment-title">
                        <a href="<?php echo JRoute::_($author->url); ?>" title="<?php echo $author->url; ?>"
                           rel="nofollow"><?php echo $author->name; ?></a>
                    </h3>
                <?php else: ?>
                    <h3 class="author uk-comment-title"><?php echo $author->name; ?></h3>
                <?php endif; ?>

                <div class="meta uk-comment-meta">
                    <?php echo $this->app->html->_('date', $comment->created, $this->app->date->format(JText::_('DATE_FORMAT_COMMENTS')), $this->app->date->getOffset()); ?>
                    | <a class="permalink" href="#comment-<?php echo $comment->id; ?>" rel="nofollow">#</a>
                </div>
                <!--/noindex-->
            </div>

            <div class="comment-body uk-comment-body">

                <div class="content"><?php echo $this->app->comment->filterContentOutput($comment->content); ?></div>

                <?php if ($comment->getItem()->isCommentsEnabled()) : ?>
                    <p><a class="reply uk-button uk-button-primary uk-button-mini" href="#" rel="nofollow"><?php echo JText::_('Reply'); ?></a>
                        <?php if ($comment->canManageComments()) : ?>

                            <?php echo ' | '; ?>
                            <a class="edit" href="#" rel="nofollow"><?php echo JText::_('Edit'); ?></a>
                            <?php echo ' | '; ?>

                            <?php if ($comment->state != Comment::STATE_APPROVED) : ?>
                                <a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=approve&comment_id=' . $comment->id; ?>"
                                   rel="nofollow"><?php echo JText::_('Approve'); ?></a>
                            <?php else: ?>
                                <a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=unapprove&comment_id=' . $comment->id; ?>"
                                   rel="nofollow"><?php echo JText::_('Unapprove'); ?></a>
                            <?php endif; ?>

                            <?php echo ' | '; ?>
                            <a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=spam&comment_id=' . $comment->id; ?>"
                               rel="nofollow"><?php echo JText::_('Spam'); ?></a>

                            <?php echo ' | '; ?>
                            <a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=delete&comment_id=' . $comment->id; ?>"
                               rel="nofollow"><?php echo JText::_('Delete'); ?></a>

                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if ($comment->state != Comment::STATE_APPROVED) : ?>
                    <div class="uk-alert">
                        <i class="uk-icon-refresh uk-icon-spin"></i>
                        <?php echo JText::_('COMMENT_AWAITING_MODERATION'); ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <?php if (count($childComments)) : ?>
            <ul class="level<?php echo ++$level; ?>">
                <?php
                foreach ($childComments as $comment) {
                    echo $this->app->jblayout->render('comment', $vars['comment'], array(
                        'author'  => $comment->getAuthor(),
                        'comment' => $comment,
                        'params'  => $params,
                        'level'   => $level,
                    ));
                }
                ?>
            </ul>
        <?php endif; ?>

    </li>

<?php
$this->app->jbdebug->mark('layout::comment(' . $vars['comment']->id . ')::finish');
