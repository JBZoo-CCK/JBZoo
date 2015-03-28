<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$mode = $this->config->get('download_mode', 'link');

$fileLink = $file['element'];
if ($mode == 'link') {
    $fileLink = $file['url'];
}

$filename = JText::_('JBZOO_ELEMENT_EMAIL_DOWNLOAD_FILE') . ' &laquo;' . $file['name'] . '&raquo;';

?>

<dt><strong><?php echo $filename; ?></strong></dt>

<dd>
    <a target="_blank" href="<?php echo $fileLink; ?>">
        <?php echo JText::_('JBZOO_ELEMENT_EMAIL_DOWNLOAD_LINKANCHOR'); ?></a>
    &nbsp;&nbsp;&nbsp;
    <span style="font-size: 13px;"><i>(<?php echo $file['size']; ?>)</i></span>
</dd>
