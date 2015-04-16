<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$pagesHTML = $modHelper->renderPages();
?>

<?php if ((int)$params->get('pages_show', 1)) : ?>
    <div class="jbfilter-row jbfilter-limit">
        <label for="jbfilter-id-limit" class="jbfilter-label">
            <?php echo JText::_('JBZOO_PAGES'); ?>
        </label>

        <div class="jbfilter-element">
            <?php echo $pagesHTML; ?>
        </div>
        <?php echo JBZOO_CLR; ?>
    </div>
<?php else :
    echo $pagesHTML;
endif;
