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

$MyItemsEdit = (int) $this->app->jbconfig->getList('config.yml')['my_items_edit'];
$MyItemsModeOnlyItemsMode =  (int) $this->app->jbconfig->getList('config.yml')['my_items_mode_listimtes'];
$OnlyMyItemsArr = $this->app->jbconfig->getList('config.yml')['only_my_listitems_rules'];
$ItemElementMode = (int) $this->app->jbconfig->getList('config.yml')['my_items_mode_listimtes_el'];
$MyMode = $this->app->jbconfig->getList('config.yml')['my_items_mode_or'];
$ItemElement = trim($this->app->jbconfig->getList('config.yml')['only_my_items_elements']);
$ItemElementRulesOption = trim($this->app->jbconfig->getList('config.yml')['only_my_items_customrules']);
$ItemElementRulesVal = trim($this->app->jbconfig->getList('config.yml')['only_my_items_value']);
$YmlVal = '';

if ($ItemElementMode == 1 && $MyItemsEdit == 1) {
    
    if (empty($ItemElementRulesOption)) {
        $ItemElementRulesOption = 'option.0';
    }

    if ($item->getElement($ItemElement)->data()) {
        $getElYmlCheck = $item->getElement($ItemElement)->data();
        $El_App = $this->app->data->create($getElYmlCheck);
        $YmlVal = $El_App->find($ItemElementRulesOption);
    }
}

?>

<?php // Простой вариант (стандартный)  ?>
<?php if ($ItemElementMode == 0 && $MyItemsEdit == 0 && $MyItemsModeOnlyItemsMode == 0 && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id]))) : ?>
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


<?php // Только выборочные items  ?>
<?php if (($ItemElementMode == 0 && $MyItemsEdit == 1 && $MyItemsModeOnlyItemsMode == 1 && (!empty($OnlyMyItemsArr) && in_array($item->id,explode(',',$OnlyMyItemsArr)))) && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id]))) : ?>

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
<?php endif;  ?>


<?php // Только выборочные items и поиск по полю НЕ содержит  ?>

<?php if (($ItemElementMode == 1 && $MyItemsEdit == 1 && $MyItemsModeOnlyItemsMode == 1 && !empty($OnlyMyItemsArr) && !empty($ItemElement) && !empty($ItemElementRulesVal) && in_array($item->id,explode(',',$OnlyMyItemsArr)) && $MyMode == 'no' && $YmlVal != $ItemElementRulesVal && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])))) : ?>

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
<?php endif;  ?>

<?php // Только выборочные items и поиск по полю СОДЕРЖИТ  ?>

<?php if (($ItemElementMode == 1 && $MyItemsEdit == 1 && $MyItemsModeOnlyItemsMode == 1 && !empty($OnlyMyItemsArr) && !empty($ItemElement) && !empty($ItemElementRulesVal) && in_array($item->id,explode(',',$OnlyMyItemsArr)) && $MyMode == 'and' && $YmlVal == $ItemElementRulesVal && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])))) : ?>

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
<?php endif;  ?>


<?php // Поиск по полю НЕ содержит  ?>

<?php if (($ItemElementMode == 1 && $MyItemsEdit == 1 && $MyItemsModeOnlyItemsMode == 0 && !empty($ItemElementRulesVal) && $MyMode == 'no' && $YmlVal != $ItemElementRulesVal && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])))) : ?>

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
<?php endif;  ?>

<?php // Поиск по полю СОДЕРЖИТ  ?>

<?php if (($ItemElementMode == 1 && $MyItemsEdit == 1 && $MyItemsModeOnlyItemsMode == 0 && !empty($ItemElementRulesVal) && $MyMode == 'and' && $YmlVal == $ItemElementRulesVal && ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])))) : ?>

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
<?php endif;