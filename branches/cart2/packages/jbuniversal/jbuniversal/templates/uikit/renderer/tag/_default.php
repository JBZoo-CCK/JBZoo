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


$this->app->jbdebug->mark('layout::tag::start');

$newItems = array();

foreach ($items as $item) {
    $newItems[$item->type][] = $item;
}

?>


<?php foreach ($newItems as $key => $newsItem) {
    $items = $newsItem;
    ?>

    <div class="module-header"><?php echo JText::_('SEARCH IN ' . $key); ?>:</div>
    <?php echo $this->partial('items', compact('items', 'is_subcategory')); ?>

<?php } ?>

<?php
$this->app->jbdebug->mark('layout::tag::finish');
