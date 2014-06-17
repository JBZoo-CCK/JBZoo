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


?>
<?php if($item_params['price'][$item->id] !=0 || !empty($item_params['price'][$item->id])) : ?>
    <offer id="<?php echo $item->id ?>">

        <url><?php echo $item_params['link'][$item->id];?></url>

        <?php if ($this->checkPosition('price')) : ?>
            <price><?php echo $item_params['price'][$item->id]; ?></price>
        <?php endif; ?>

        <currencyId><?php echo $item_params['currencyId'][$item->id]; ?></currencyId>
        <categoryId><?php echo $item_params['categoryId'][$item->id]; ?></categoryId>

        <?php if ($this->checkPosition('image')) : ?>
            <picture><?php echo $item_params['picture'][$item->id];?></picture>
        <?php endif; ?>

        <?php if ($this->checkPosition('title')) : ?>
            <name><?php echo $this->app->jbyml->replaceSpecial($item->name); ?></name>
        <?php endif; ?>

        <?php if ($this->checkPosition('vendor')) : ?>
            <vendor><?php echo $this->app->jbyml->replaceSpecial($this->renderPosition('vendor')) ?></vendor>
        <?php endif; ?>

        <available><?php echo $this->app->jbyml->replaceSpecial($item_params['available'][$item->id]); ?></available>

        <?php if ($this->checkPosition('description')) : ?>
            <description><?php echo $this->app->jbyml->replaceSpecial($this->renderPosition('description')) ?></description>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <?php echo $this->renderPosition('properties', array('style' => 'jbxml')) ?>
        <?php endif; ?>

    </offer>
<?php endif;
