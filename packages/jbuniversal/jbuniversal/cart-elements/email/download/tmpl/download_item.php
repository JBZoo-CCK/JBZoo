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
