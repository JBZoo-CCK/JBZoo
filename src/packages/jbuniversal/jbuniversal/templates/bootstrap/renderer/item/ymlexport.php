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

$yml = $this->app->jbyml;

?>

<?php if ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])) : ?>
    <offer id="<?php echo $item->id ?>"
           available="<?php echo $yml->replaceSpecial($item_params['available'][$item->id]); ?>">

        <url><?php echo $yml->replaceSpecial($item_params['link'][$item->id]); ?></url>

        <price><?php echo str_replace(' ', '', $item_params['price'][$item->id]); ?></price>

        <?php if ($item_params['priceOld'][$item->id] > 0) : ?>
            <oldprice><?php echo $item_params['priceOld'][$item->id]; ?></oldprice>
        <?php endif; ?>

        <currencyId><?php echo $item_params['currencyId'][$item->id]; ?></currencyId>
        <categoryId><?php echo $item_params['categoryId'][$item->id]; ?></categoryId>

        <?php if ($this->checkPosition('image')) {
            if (is_array($item_params['picture'][$item->id])) {
                foreach ($item_params['picture'][$item->id] as $image) {
                    echo '<picture>' . $image . "</picture>\n ";
                }
            } else {
                echo '<picture>' . $item_params['picture'][$item->id] . '</picture>';
            }
        } ?>

        <?php if ($this->checkPosition('title')) : ?>
            <name><?php echo $yml->replaceSpecial($this->renderPosition('title')); ?></name>
        <?php endif; ?>

        <?php if ($this->checkPosition('vendor')) : ?>
            <vendor><?php echo $yml->replaceSpecial($this->renderPosition('vendor')) ?></vendor>
        <?php endif; ?>

        <?php if ($this->checkPosition('description')) : ?>
            <description><?php echo $yml->replaceSpecial($this->renderPosition('description')) ?></description>
        <?php endif; ?>

        <?php if ($this->checkPosition('country_of_origin') && !empty($item_params['country'][$item->id])) : ?>
            <country_of_origin><?php echo $yml->replaceSpecial($item_params['country'][$item->id]); ?></country_of_origin>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <?php echo $this->renderPosition('properties', array('style' => 'jbxml')) ?>
        <?php endif; ?>
    </offer>
<?php endif; ?>