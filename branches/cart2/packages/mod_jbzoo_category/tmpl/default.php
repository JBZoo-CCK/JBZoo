<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$borderClass = (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : '';
$uniqId = uniqid('jbzoo-');
$classes = array('yoo-zoo', 'jbzoo', 'jbzoo-category-module', 'jbcategory-layout-tab', $borderClass);

?>
<?php if (!empty($categories)): ?>
    <div id="<?php echo $uniqId ?>" class='<?php echo implode(' ', $classes); ?>'>

        <?php foreach ($categories as $catId => $category): ?>
            <div class="category-wrapper <?php echo $category['active_class']; ?>">

                <div class="jbcategory rborder">
                    <?php if (!empty($category['image'])): ?>
                        <div class="jbcategory-image align-<?php echo $params->get('category_image_align', 'left') ?>">
                            <a href="<?php echo $category['cat_link'] ?>"
                               title="<?php echo $category['category_name'] ?>"><?php echo $category['image'] ?></a>
                        </div>
                    <?php endif; ?>

                    <div class="jbcategory-link">
                        <a href="<?php echo $category['cat_link'] ?>" title="<?php echo $category['category_name'] ?>">
                            <?php echo $category['category_name'];
                            if (!empty($category['item_count'])) {
                                echo ' (' . $category['item_count'] . ')';
                            }
                            ?></a>
                    </div>

                    <?php if (!empty($category['desc'])): ?>
                        <p class="jbcategory-desc"><?php echo $category['desc'] ?></p>
                    <?php endif; ?>

                    <div class="clear"></div>
                </div>

                <?php if (!empty($category['items'])) {
                    echo '<div class="jbcategory-items">';

                    $layout = $params->get('item_layout', 'default');
                    foreach ($category['items'] as $itemId => $item) {
                        $itemClasses = array(
                            'jbzoo-item',
                            'jbzoo-item-' . $layout,
                            'jbzoo-item-' . $item->type,
                            'jbzoo-item-' . $item->id,
                            'rborder',
                        );
                        ?>
                        <div class="<?php echo implode(' ', $itemClasses); ?>">
                            <?php echo $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params)); ?>
                            <div class="clear"></div>
                        </div>
                    <?php
                    }
                    echo '</div>';
                } ?>
            </div>
        <?php endforeach; ?>

        <div class="clear"></div>
    </div>
<?php endif; ?>
