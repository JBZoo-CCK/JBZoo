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
$uniqId = uniqid('jbzoo-tabs-');
$classes = array('yoo-zoo', 'jbzoo', 'jbzoo-category-module', 'jbcategory-layout-tab', $borderClass);

if (!empty($categories)): ?>
    <div id="<?php echo $uniqId ?>" class="<?php echo implode(' ', $classes); ?>">

        <ul>
            <?php foreach ($categories as $catId => $category): ?>
                <li class="<?php echo $category['active_class']; ?>"><a href="#category-tab-<?php echo $catId ?>"><?php
                        echo $category['category_name'];
                        if (!empty($category['item_count'])) {
                            echo ' (' . $category['item_count'] . ')';
                        }
                        ?></a></li>
            <?php endforeach; ?>
        </ul>

        <?php foreach ($categories as $catId => $category): ?>
            <div id="category-tab-<?php echo $catId ?>">

                <?php if (!empty($category['image']) || !empty($category['desc'])) : ?>
                    <div class="jbcategory rborder">
                        <div class="jbcategory-image">

                            <?php if (!empty($category['image'])): ?>
                                <div class="align-<?php echo $params->get('category_image_align', 'left') ?>">
                                    <a href="<?php echo $category['cat_link'] ?>"><?php echo $category['image'] ?></a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($category['desc'])): ?>
                                <p class="jbcategory-desc"><?php echo $category['desc'] ?></p>
                            <?php endif; ?>

                            <div class="clear"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                if (!empty($category['items'])) {
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
                }
                ?>
            </div>

        <?php endforeach; ?>
    </div>

    <script type="text/javascript">
        jQuery(function ($) {
            $('#<?php echo $uniqId ?>').JBZooTabs({
                onTabShow: function (index) {
                    var map = $('.googlemaps > div:first');
                    if (map.length) {
                        map.data('Googlemaps').refresh();
                    }
                }
            });
        });
    </script>

<?php endif;
