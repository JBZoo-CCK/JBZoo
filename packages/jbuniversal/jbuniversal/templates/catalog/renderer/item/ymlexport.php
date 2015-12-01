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


$yml = $this->app->jbyml;
?>

<?php if ($item_params['price'][$item->id] != 0 || !empty($item_params['price'][$item->id])) : ?>
    <offer id="<?php echo $item->id ?>"
           available="<?php echo $yml->replaceSpecial($item_params['available'][$item->id]); ?>">

        <url><?php echo $yml->replaceSpecial($item_params['link'][$item->id]); ?></url>

        <price><?php echo $item_params['price'][$item->id]; ?></price>

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
