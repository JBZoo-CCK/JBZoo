<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="jbzoo-bookmarks">
    <?php foreach ($bookmarks as $name => $data) : ?>
        <?php
        if ($name == "email") {
            $title = JText::_('JBZOO_BOOKMARK_RECOMMENDED_THIS_PAGE_TO');
        } else {
            $title = JText::_('JBZOO_BOOKMARK_ADD_THIS_PAGE_TO') . ' ' . $data['label'];
        }
        ?>

        <a class="<?php echo $name ?>" onclick="<?php echo $data['click']; ?>" href="<?php echo $data['link']; ?>"
           title="<?php echo $title; ?>"></a>
    <?php endforeach; ?>
</div>
