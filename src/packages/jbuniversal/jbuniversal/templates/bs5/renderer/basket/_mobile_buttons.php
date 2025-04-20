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

<div class="text-center jbform-actions uk-clearfix">

    <input type="submit" name="create" value="<?php echo JText::_('JBZOO_CART_MOBILE_SUBMIT'); ?>"
           class="btn btn-success btn-lg" />

    <?php if ($view->payment) : ?>
        <input type="submit" name="create-pay" value="<?php echo JText::_('JBZOO_CART_MOBILE_SUBMIT_AND_PAY'); ?>"
               class="btn btn-success btn-lg" />
    <?php endif; ?>

</div>
