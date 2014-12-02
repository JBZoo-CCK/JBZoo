<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

?>

<div class="name-input">
    <label for="name">Name</label>
    <input type="text" name="<?php echo $var . '[option][' . $num . '][name]'; ?>" value="<?php echo $name; ?>"/>
</div>

<div class="value-input">
    <label for="value">Value</label>
    <a class="trigger" href="#" title="<?php echo JText::_('Edit Option Value'); ?>"><?php echo $value; ?></a>

    <div class="panel">
        <input type="text" name="<?php echo $var . '[option][' . $num . '][value]'; ?>" value="<?php echo $value; ?>"/>
        <input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>">
        <a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
    </div>
</div>

<div class="delete" title="<?php echo JText::_('Delete option'); ?>">
    <img alt="<?php echo JText::_('Delete option'); ?>"
         src="<?php echo $this->app->path->url('assets:images/delete.png'); ?>"/>
</div>

<div class="sort-handle" title="<?php echo JText::_('Sort option'); ?>">
    <img alt="<?php echo JText::_('Sort option'); ?>"
         src="<?php echo $this->app->path->url('assets:images/sort.png'); ?>"/>
</div>
