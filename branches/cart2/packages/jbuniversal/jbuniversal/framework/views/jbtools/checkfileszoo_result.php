<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


?>
<div class="creation-form">

    <h3><em><?php echo JText::_('JBZOO_MODIFICATIONS'); ?></em></h3>

    <?php if (empty($this->results)) : ?>

        <div class="infobox"><?php echo JText::_('JBZOO_MODIFICATIONS_NOT_FOUND'); ?></div>

    <?php else: ?>

        <div class="infobox"><?php echo JText::_('JBZOO_MODIFICATIONS_DESCRIPTIONS'); ?></div>

        <?php foreach ($this->results as $type => $result) : ?>
            <div class="importbox">
                <div>
                    <h3><em><?php echo JText::_('JBZOO_MODIFICATIONS_' . $type); ?>:</em></h3>
                    <ul class="<?php echo $type; ?>">
                        <?php foreach ($result as $file) : ?>
                            <li><?php echo $file; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
