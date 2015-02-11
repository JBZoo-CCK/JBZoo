<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$count = count($items);
$zoo   = App::getInstance('zoo');

if ($count) : ?>

    <div id="<?php echo $unique; ?>" class="jbzoo yoo-zoo">
        <div class="module-items">

            <?php if ($params->get('delete') && $params->get('mode') == 'viewed') : ?>
                <a href="index.php?option=com_zoo&controller=viewed&task=clear&format=raw"
                   class="jsRecentlyViewedClear recently-viewed-clear">
                    <?php echo JText::_('JBZOO_MODITEM_DELETE'); ?>
                </a>
            <?php endif; ?>

            <table class="wrapper-item-desc">
                <?php
                foreach ($items as $item) {
                    $app_id = $item->application_id;
                    echo $renderer->render('item.' . $params->get('item_layout', 'table'),
                        array(
                            'item'   => $item,
                            'params' => $params
                        )
                    );
                }
                ?>
            </table>
        </div>
    </div>

    <?php
    if ($params->get('delete') && $params->get('mode') == 'viewed') {
        echo $zoo->jbassets->widget('#' . $unique, 'JBZooViewed', array(
            'message' => JText::_('JBZOO_MODITEM_RECENTLY_VIEWED_DELETE_HISTORY'),
            'app_id'  => $app_id,
        ), true);
    } ?>

<?php endif;