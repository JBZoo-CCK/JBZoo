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
            'width'       => '100%'
        )) .
        $this->getStyles(array(
            'border-collapse' => 'collapse',
            'font-size'       => '14px',
            'width'           => '100%'
        )); ?>>

        <?php if ($this->checkPosition('title')) : ?>
            <tr>
                <td colspan="2">
                    <h2><?php echo $this->renderPosition('title'); ?></h2>
                    <hr>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="2" valign="top">
                <?php echo $this->renderPosition('items', array('style' => 'block')); ?>
            </td>
        </tr>

        <tr>
            <td valign="top" width="50%">
                <?php echo $this->renderPosition('info', array('style' => 'block')); ?>
                <?php echo $this->renderPosition('payment', array('style' => 'block')); ?>
            </td>
            <td valign="top">
                <?php echo $this->renderPosition('shipping', array('style' => 'block')); ?>
            </td>
        </tr>

    </table>

<?php if ($this->checkPosition('other')) : ?>
    <table>
        <?php echo $this->renderPosition('other', array('style' => 'table-row')); ?>
    </table>
<?php endif;
