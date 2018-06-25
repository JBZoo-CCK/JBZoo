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
$zoo->jbassets->uikit(false, true);

$categories = $modHelper->getCategories();

$attrs = array(
    'id'    => $modHelper->getModuleId(),
    'class' => array(
        'yoo-zoo', // for Zoo widgets
        'jbzoo',
        'jbcategory-module',
        'jbcategory-module-uikit',
        (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : '',
        'uk-clearfix'
    ),
);

?>
<?php if (!empty($categories)): ?>
    <div <?php echo $modHelper->attrs($attrs) ?>>

        <?php foreach ($categories as $catId => $category) : ?>
            <div
                class="category-wrapper uk-panel uk-panel-box uk-article-divider <?php echo $category['active_class']; ?>">

                <div class="jbcategory uk-clearfix">
                    <?php if (!empty($category['image'])): ?>
                        <div
                            class="jbcategory-image uk-align-<?php echo $params->get('category_image_align', 'left') ?>">
                            <a href="<?php echo $category['cat_link'] ?>" class="uk-thumbnail"
                               title="<?php echo $category['category_name'] ?>"><?php echo $category['image'] ?></a>
                        </div>
                    <?php endif; ?>

                    <div class="jbcategory-link">
                        <a href="<?php echo $category['cat_link'] ?>" title="<?php echo $category['category_name'] ?>">
                            <?php echo $category['category_name'];
                            if (!empty($category['item_count'])) {
                                echo ' (' . $category['item_count'] . ')';
                            }
                            ?>
                        </a>
                    </div>

                    <?php if (!empty($category['desc'])): ?>
                        <p class="jbcategory-desc"><?php echo $category['desc'] ?></p>
                    <?php endif; ?>
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
                            'uk-panel uk-panel-box',
                            'uk-article-divider uk-clearfix',
                        );

                        $renderer = $modHelper->createRenderer('item');
                        ?>
                        <div class="<?php echo implode(' ', $itemClasses); ?>">
                            <?php echo $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params)); ?>
                        </div>
                    <?php
                    }
                    echo '</div>';
                } ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
