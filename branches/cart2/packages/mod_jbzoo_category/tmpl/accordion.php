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

$uniqId = uniqid('jbzoo-accordion-');
$borderClass = (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : '';
$classes = array('yoo-zoo', 'jbzoo', 'jbzoo-category-module', 'jbcategory-layout-accordion', $borderClass);

$html = array();

if (!empty($categories)) {
    $html[] = '<div id="' . $uniqId . '" class="' . implode(' ', $classes) . '">';

    foreach ($categories as $catId => $category) {

        // only if items exists
        if (empty($category['items'])) {
            continue;
        }

        // output category - start
        // check image
        if (!empty($category['image'])) {
            $html[] = '<div class="jbaccordion-header ' . $category['active_class'] . '" style="line-height: ' . $category['attr']['height'] . 'px">';
            $html[] = '<div class="align-' . $params->get('category_image_align', 'left') . '">' . $category['image'] . '</div>';
        } else {
            $html[] = '<div class="jbaccordion-header ' . $category['active_class'] . '">';
        }

        // add items count
        $html[] = '<div class="jbcategory-header">'
            . $category['category_name']
            . ((!empty($category['item_count'])) ? ' (' . $category['item_count'] . ')' : '')
            . '</div>';

        $html[] = '<div class="clear"></div></div>';
        // output category - finish


        // output items - start
        $html[] = '<div class="jbcategory-items">';
        if (!empty($category['desc'])) {
            $html[] = '<p class="jbcategory-desc">' . $category['desc'] . '</p>';
        }

        // render item
        $layout = $params->get('item_layout', 'default');
        foreach ($category['items'] as $itemId => $item) {
            $itemClasses = array(
                'rborder',
                'jbzoo-item',
                'jbzoo-item-' . $layout,
                'jbzoo-item-' . $item->type,
                'jbzoo-item-' . $item->id,
            );

            $html[] = '<div class="' . implode(' ', $itemClasses) . '">';
            $html[] = $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params));
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = '</div>';
        // output items - finish

    }
    $html[] = '</div>';
}

if (!empty($html)) {
    echo implode("\n ", $html);
    ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $("#<?php echo $uniqId ?>").JBZooAccordion({
                headerWidget: '.jbaccordion-header'
            });
        });
    </script>
<?php
}
