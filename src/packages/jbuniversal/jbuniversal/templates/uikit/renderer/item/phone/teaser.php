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

?>
<div class="jbzoo-teaser-shadow">
    <div class="jbzoo-teaser-wrapper">
        <?php if ($this->checkPosition('image')) { ?>
            <div class="uk-clearfix jbzoo-no-border jbzoo-image uk-text-center">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php }

        if ($this->checkPosition('title')) { ?>
            <div class="jbzoo-item-title">
                <?php echo $this->renderPosition('title'); ?>
            </div>
        <?php }

        if ($this->checkPosition('price') || $this->checkPosition('rating')) { ?>
            <div class="jbzoo-info uk-clearfix uk-margin-small">
                <div class="jbzoo-info-price uk-float-left uk-width-small-1-2">
                    <?php echo $this->renderPosition('price'); ?>
                </div>

                <div class="jbzoo-info-rating uk-float-left uk-width-small-1-2">
                    <?php echo $this->renderPosition('rating'); ?>
                </div>
            </div>
        <?php }

        if ($this->checkPosition('tools-user') || $this->checkPosition('tools-buttons')) { ?>
            <div class="jbzoo-tools uk-clearfix uk-margin-small">

                <div class="jbzoo-tools-buttons jbzoo-tools-left">
                    <?php echo $this->renderPosition('tools-buttons'); ?>
                </div>

                <div class="jbzoo-tools-right">
                    <?php echo $this->renderPosition('tools-user', [
                            'style' => 'jbblock',
                            'class' => 'uk-display-inline-block'
                        ]
                    ); ?>
                </div>

            </div>
        <?php }

        if ($this->checkPosition('properties')) {
            echo $this->renderPosition('properties', ['style' => 'list']);
        } ?>
    </div>
</div>