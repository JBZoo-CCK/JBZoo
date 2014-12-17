<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="<?php echo $this->identifier; ?>">

    <div class="download-select">

        <div class="upload">
            <input type="text" id="filename<?php echo $this->identifier; ?>" readonly="readonly"/>

            <div class="button-container">
                <input type="file" name="elements_<?php echo $this->identifier; ?>"
                       onchange="javascript: document.getElementById('filename<?php echo $this->identifier; ?>').value = this.value.replace(/^.*[\/\\]/g, '');"/>
            </div>
        </div>

        <input type="hidden" class="upload" name="<?php echo $this->getControlName('upload'); ?>"
               value="<?php echo $upload ? 1 : ''; ?>"/>

    </div>

    <div class="download-preview">
        <span class="preview"><?php echo $upload; ?></span>
        <span class="download-cancel" title="<?php JText::_('Remove file'); ?>"></span>
    </div>

</div>