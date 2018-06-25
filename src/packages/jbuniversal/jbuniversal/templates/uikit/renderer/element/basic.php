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

$type = strtoupper($params['type']);
$elId = $this->app->jbstring->getId('basic-');

?>

<div class="jbpriceadv-row basic-<?php echo strtolower($type); ?>-wrap clearfix">
    <label for="<?php echo $elId . '-' . $type; ?>" class="hasTip row-field"
           title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_' . $type . '_DESC'); ?>">
        <?php echo JText::_('JBZOO_JBPRICE_BASIC_' . $type); ?>
    </label>

    <?php echo $element->edit(); ?>

    <?php echo JBZOO_CLR; ?>
</div>