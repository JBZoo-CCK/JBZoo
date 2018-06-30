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

$categories = $modHelper->getCategories();

$attrs = array(
    'id'    => $modHelper->getModuleId(),
    'class' => array(
        'yoo-zoo', // for Zoo widgets
        'jbzoo',
        'jbcategory-module',
        'jbcategory-module-default',
        (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : ''
    ),
);

?>

<?php if (!empty($categories)): ?>
    <div <?php echo $modHelper->attrs($attrs) ?>>

        <?php foreach ($categories as $catId => $category) : ?>
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

                    <?php echo JBZOO_CLR; ?>
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
                        $renderer    = $modHelper->createRenderer('item');

                        ?>
                        <div class="<?php echo implode(' ', $itemClasses); ?>">
                            <?php echo $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params)); ?>
                            <?php echo JBZOO_CLR; ?>
                        </div>
                    <?php
                    }
                    echo '</div>';
                } ?>
            </div>
        <?php endforeach; ?>

        <?php echo JBZOO_CLR; ?>
    </div>
<?php endif; ?>
