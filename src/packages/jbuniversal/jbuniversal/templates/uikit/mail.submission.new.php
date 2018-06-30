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

// author
$user_name = JText::_('Guest');
if ($author = $item->created_by_alias) {
    $user_name = $author;
} else if (($user = $item->app->user->get($item->created_by)) && $user->name) {
    $user_name = $user->name;
}

?>

<html>
<body>
<p>Hi,</p>

<p>You are receiving this email because you are administaring the submissions at <?php echo $website_name; ?>. There has
    been a new submission by <?php echo $user_name; ?> - <?php echo $item->name; ?>.</p>

<p>If you want to edit the new submission, click the following link:
    <a href="<?php echo $item_link; ?>"><?php echo $item_link; ?></a></p>
</body>
</html>