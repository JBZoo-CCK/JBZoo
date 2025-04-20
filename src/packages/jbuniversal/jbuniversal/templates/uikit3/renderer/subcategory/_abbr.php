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


$this->app->jbdebug->mark('layout::subcategory(' . $vars['object']->id . ')::start');

// set vars
$subcategory = $vars['object'];
$params = $subcategory->getParams('site');
$link = $this->app->route->category($subcategory);
$task = $this->app->jbrequest->get('task', 'category');

// teaser content
$text = $params->get('content.category_teaser_text', '');
$imageAlign = $params->get('template.subcategory_teaser_image_align', 'left');

// items
$itemsOrder = $vars['params']->get('config.item_order', 'none');
$maxItems = $vars['params']->get('template.subcategory_items_count', 5);
$showCount = $vars['params']->get('template.subcategory_items_count_show', 1);

$items = array();
$countItems = 0;
if ($showCount || $maxItems != "0" || $maxItems == "-1") {
    $items      = JBModelCategory::model()->getItemsByCategory($subcategory->application_id, $subcategory->id, $itemsOrder, $maxItems);
    $countItems = $subcategory->itemCount();
}

$image = $this->app->jbimage->get('category_teaser_image', $params);
$abbr =  $subcategory->params->get('content.category_teaser_abbr', '');
?>
    <div class="subcategory clearfix subcategory-<?php echo $subcategory->alias; ?>">

        <?php if ($vars['params']->get('template.subcategory_teaser_image', 1) && $image['src']) : ?>
            <div class="subcategory-image align-<?php echo $imageAlign; ?>">
                <a href="<?php echo $link; ?>" title="<?php echo $subcategory->name; ?>"><img
                        src="<?php echo $image['src']; ?>" <?php echo $image['width_height']; ?>
                        alt="<?php echo $subcategory->name; ?>"
                        title="<?php echo $subcategory->name; ?>"
                        /></a>
            </div>
        <?php endif; ?>


        <h2 class="subcategory-title">
            <a href="<?php echo $link; ?>"
               title="<?php echo $subcategory->name; ?>">
                <?php echo (!empty($abbr)?$abbr:$subcategory->name) ?>
            </a>
            <?php if ($showCount && $countItems != 0) : ?><span>(<?php echo $countItems; ?>)</span><?php endif; ?>
        </h2>

        <?php
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__zoo_category')
            ->where('`parent` = '.(int)$subcategory->id);
        $subCats = $db->setQuery($query)->loadObjectList();
        ?>


        <?php if (!empty($subCats)) { ?>
            <div class="abbr">
                <?php $ip = 1; ?>
                <?php foreach ($subCats as $subCat) { ?>
                    <?php $c = $this->app->table->category->get($subCat->id) ?>
                    <?php if ($c->getParams()->get('content.category_teaser_abbr')) : ?>
                        <span class="jbzoo-item jbzoo-item-product-armatura-a-as jbzoo-item-subcategory_item jbzoo-item-4">
                            <?php if($ip!=1) { ?> | <?php } ?>
                            <span class="item-title">
                                 <a href="<?php echo  $this->app->route->category($c); ?>" title="<?php echo $subCat->name; ?>">
                                    <?php echo $c->getParams()->get('content.category_teaser_abbr'); ?>
                                 </a>
                            </span>
                        </span>
                        <?php $ip++; ?>
                    <?php endif; ?>

                <?php } ?>
            </div>
        <?php } ?>



        <?php /*if (in_array($task, array('category', 'frontpage'))) : ?>
            <?php if ($maxItems != 0 && count($items) > 0) : ?>
                <div class="subcategory-items clearfix">
                    <?php
                    $i=0;
                    foreach ($items as $item) {
                        echo ($i==0?'':', ').$this->app->jblayout->renderItem($item, 'subcategory_item');
                        $i++;
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; */?>
    </div>

<?php
$this->app->jbdebug->mark('layout::subcategory(' . $vars['object']->id . ')::finish');
?>
<style>
    .subcategory-items .jbzoo-item {
        display: inline-block;
    }
</style>
