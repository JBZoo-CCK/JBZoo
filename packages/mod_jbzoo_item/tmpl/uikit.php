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

$count = count($items);
$columns = (int)$params->get('item_cols', 1);
$border = (int)$params->get('display_border', 1) ? 'rborder' : 'no-border';

if ($count) {

    echo '<div id="' . $unique . '" class="jbzoo yoo-zoo">';
    echo '<div class="module-items jbzoo-' . $border . ' module-items-col-' . $columns . '">';

    if ($params->get('delete') && $params->get('mode') == 'viewed') {
        echo '<a href="index.php?option=com_zoo&controller=viewed&task=clear&format=raw" class="jsRecentlyViewedClear recently-viewed-clear uk-button uk-button-danger">' .
                '<i class="uk-icon-trash"></i>&nbsp;' .
                JText::_('JBZOO_MODITEM_DELETE') .
            '</a>';
    }

    if ($columns) {

        $i = 0;
        $j = 0;

        $rowItem = array_chunk($items, $columns);

        echo '<div class="items items-col-' . $columns . ' uk-article-divider">';

        foreach ($rowItem as $row) {
            echo '<div class="uk-grid item-row-' . $i . '" data-uk-grid-margin>';

                foreach ($row as $item) {

                    $app_id = $item->application_id;
                    $first  = ($j == 0) ? ' first' : '';
                    $last   = ($j == $count - 1) ? ' last' : '';
                    $j++;

                    $isLast = $j % $columns == 0;

                    if ($isLast) {
                        $last .= ' last';
                    }

                    echo '<div class="item-column uk-width-medium-1-' . $columns . $first . $last . '">' .
                            '<div class="uk-panel uk-panel-box">' .
                                $renderer->render('item.' . $params->get('item_layout', 'default'),
                                    array(
                                        'item'   => $item,
                                        'params' => $params
                                    )
                                ) .
                            '</div>' .
                        '</div>';
                }

            $i++;

            echo '</div>';
        }

        echo '</div>';



    } else {

        foreach ($items as $item) {
            $app_id = $item->application_id;
            echo $renderer->render('item.' . $params->get('item_layout', 'default'),
                array(
                    'item'   => $item,
                    'params' => $params
                )
            );
        }
    }

    echo '</div></div>';

    if ($params->get('delete') && $params->get('mode') == 'viewed') : ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#<?php echo $unique; ?>').JBZooViewed({
                    'message': '<?php echo JText::_('JBZOO_MODITEM_RECENTLY_VIEWED_DELETE_HISTORY'); ?>',
                    'app_id' :  <?php echo $app_id; ?>
                });
            });
        </script>
    <?php endif; ?>

    <?php if ($params->get('column_heightfix')) : ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.module-items').JBZooHeightFix();
            });
        </script>
    <?php endif;

}
