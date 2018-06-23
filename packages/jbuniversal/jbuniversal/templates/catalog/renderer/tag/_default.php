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
