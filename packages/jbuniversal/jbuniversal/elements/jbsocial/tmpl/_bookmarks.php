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
