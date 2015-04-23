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

$items = $modHelper->getItems();
$count = count($items);

if ($count) : ?>

    <div id="<?php echo $modHelper->getModuleId(); ?>" class="jbzoo yoo-zoo">
        <div class="module-items">

            <?php echo $modHelper->renderRemoveButton(); ?>

            <table class="wrapper-item-desc">
                <?php
                foreach ($items as $item) {
                    $renderer = $modHelper->createRenderer('item');
                    echo $renderer->render('item.' . $modHelper->getItemLayout(), array(
                        'item'   => $item,
                        'params' => $params
                    ));
                }
                ?>
            </table>
        </div>
    </div>

<?php endif;
