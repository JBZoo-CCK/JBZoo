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
$zoo->jbassets->accordion();

$categories = $modHelper->getCategories();

$attrs = array(
    'id'    => $modHelper->getModuleId(),
    'class' => array(
        'yoo-zoo', // for Zoo widgets
        'jbzoo',
        'jbcategory-module',
        'jbcategory-module-accordion',
        (int)$params->get('category_display_border', 0) ? 'jbzoo-rborder' : ''
    ),
);

$html = array();
if (!empty($categories)) {

    $html[] = '<div ' . $modHelper->attrs($attrs) . '>';

    foreach ($categories as $catId => $category) {

        // only if items exists
        if (empty($category['items'])) {
            continue;
        }

        // output category
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

        $html[] = JBZOO_CLR . '</div>';

        $html[] = '<div class="jbcategory-items">';
        if (!empty($category['desc'])) {
            $html[] = '<p class="jbcategory-desc">' . $category['desc'] . '</p>';
        }

        // output items
        $layout = $params->get('item_layout', 'default');
        foreach ($category['items'] as $itemId => $item) {
            $itemClasses = array(
                'rborder',
                'jbzoo-item',
                'jbzoo-item-' . $layout,
                'jbzoo-item-' . $item->type,
                'jbzoo-item-' . $item->id,
            );

            $renderer = $modHelper->createRenderer('item');

            $html[] = '<div class="' . implode(' ', $itemClasses) . '">';
            $html[] = $renderer->render('item.' . $layout, array('item' => $item, 'params' => $params));
            $html[] = JBZOO_CLR;
            $html[] = '</div>';
        }

        $html[] = '</div>';
    }

    $html[] = '</div>';
}

if (!empty($html)) {

    $html[] = $zoo->jbassets->widget('#' . $modHelper->getModuleId(), 'JBZoo.Accordion', array(
        'headerWidget' => '.jbaccordion-header'
    ), true);

    echo implode(PHP_EOL, $html);
}
