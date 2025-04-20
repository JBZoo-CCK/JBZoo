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


<div class="card__cover card__cover_main-promobox alignment-center itemcolor">

<?php if ($this->checkPosition('media')) : ?>

<div class="cover">

<?php if ($this->checkPosition('icons')) : ?>
    <div class="item-icons"><?php echo $this->renderPosition('icons'); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('media')) : ?>
	<div class="item-media"><?php echo $this->renderPosition('media'); ?></div>
<?php endif; ?>

</div>

<?php endif; ?>


<?php if ($this->checkPosition('meta')) : ?>
	<div class="item-meta"><?php echo $this->renderPosition('meta'); ?></div>
<?php endif; ?>


<?php if ($this->checkPosition('date')) : ?>
	<div class="card__date card__trend_with-label"><?php echo $this->renderPosition('date', array('style' => 'comma')); ?></div>
<?php endif; ?>

</div>


<div class="card__content"> <!-- Новый div для контента -->

<?php if ($this->checkPosition('title')) : ?>
	<div class="card__heading card__heading_main-promobox card__heading_main-promobox_2"><?php echo $this->renderPosition('title'); ?></div>
<?php endif; ?>

<div class="card__summary-wrapper"> <!-- New wrapper div -->
	<?php if ($this->checkPosition('description')) : ?>
	<div class="card__summary card__summary_main-promobox card__summary_main-promobox_2"><?php echo $this->renderPosition('description', array('style' => 'block')); ?></div>
	<?php endif; ?>
</div>
	

<?php if ($this->checkPosition('media')) : ?>
	<div class="item-media"><?php echo $this->renderPosition('media'); ?></div>
<?php endif; ?>


	<?php if ($this->checkPosition('links')) : ?>
	<p class="links"><?php echo $this->renderPosition('links', array('style' => 'pipe')); ?></p>
	<?php endif; ?>

</div>

</div>