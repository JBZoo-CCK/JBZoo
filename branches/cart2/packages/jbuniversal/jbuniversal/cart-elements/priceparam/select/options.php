<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get element from parent parameter form
$element = $parent->element;
$config  = $element->config;
$url = $this->app->link(array('controller' => 'manager', 'format' => 'raw', 'task' => 'getalias', 'force_safe' => 1), false);

// init vars
$id = uniqid('option-');
$i  = 0;

?>

<div id="<?php echo $id; ?>" class="options">
	<ul>
		<?php
			foreach ($config->get('option', array()) as $opt) {
				echo '<li>'.$element->editOption($control_name, $i++, $opt['name'], $opt['value']).'</li>';
			}
		?>
		<li class="hidden" ><?php echo $element->editOption($control_name, '0', '', ''); ?></li>
	</ul>
	<div class="add"><?php echo JText::_('Add Option'); ?></div>
</div>

<script type="text/javascript">
	jQuery('#<?php echo $id; ?>').ElementSelect({variable: '<?php echo $control_name; ?>', url : '<?php echo $url; ?>'});
</script>