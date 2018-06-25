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
<table <?php echo $this->getAttrs(array(
        'cellpadding' => 8,
        'width'       => '800px'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse',
        'font-size'       => '14px',
        'width'           => '800px'
    )); ?>>

    <?php if ($this->checkPosition('title')) : ?>
        <tr>
            <td>
                <h2><?php echo $this->renderPosition('title'); ?></h2>
                <hr>
            </td>
        </tr>
    <?php endif; ?>

    <?php echo $this->renderPosition('body', array('style' => 'table-row')); ?>

</table>
