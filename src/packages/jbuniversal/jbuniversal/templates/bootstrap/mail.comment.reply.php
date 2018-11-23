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

<html>
<body>
<p>Hi <?php echo $name; ?>,</p>

<p>You are receiving this email because you are watching the topic, <?php echo $item->name; ?>
    at <?php echo $website_name; ?>. This topic has received a reply.</p>

<p>Quote: "<?php echo $comment->content; ?>"</p>

<p>If you want to view the post made, click the following link:
    <a href="<?php echo $comment_link; ?>"><?php echo $comment_link; ?></a></p>

<p>If you want to view the topic, click the following link:
    <a href="<?php echo $item_link; ?>"><?php echo $item_link; ?></a></p>

<p>If you want to view the website, click on the following link:
    <a href="<?php echo $website_link; ?>"><?php echo $website_link; ?></a></p>

<p>If you no longer wish to watch this topic, click the following link:
    <a href="<?php echo $unsubscribe_link; ?>"><?php echo $unsubscribe_link; ?></a></p>
</body>
</html>