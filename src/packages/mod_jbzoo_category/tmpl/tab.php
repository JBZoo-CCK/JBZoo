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

$zoo = App::getInstance('zoo');
$zoo->jbassets->tabs();

$categories = $modHelper->getCategories();

$attrs = array(
    'id'    => $modHelper->getModuleId(),
    'class' => array(
        'yoo-zoo', // for Zoo widgets
        'jbzoo',
        'jbcategory-module',
        'jbcategory-module-tab',
        (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : ''
    ),
);

if (!empty($categories)): ?>
    <div <?php echo $modHelper->attrs($attrs) ?>>

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

                            <?php echo JBZOO_CLR; ?>
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

                        $renderer = $modHelper->createRenderer('item');
                        ?>
                        <div class="<?php echo implode(' ', $itemClasses); ?>">
                            <?php echo $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params)); ?>
                            <?php echo JBZOO_CLR; ?>
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
            $('#<?php echo $modHelper->getModuleId() ?>').JBZooTabs({
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
