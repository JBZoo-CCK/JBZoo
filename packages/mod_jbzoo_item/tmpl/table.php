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
