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