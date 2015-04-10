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

$this->app->jbassets->initTooltip();
$this->app->jbassets->less('jbassets:less/general.less');

$isShowlabel = (int)$params->get('showlabel');
if ($isShowlabel) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
}

?>

<?php if ($isShowlabel) : ?>
    <dt class="element-label">
        <?php echo $label; ?>
        <?php if ($description = $element->getDescription()) : ?>
            <span class="jbtooltip" title="<?php echo $description; ?>"> </span>
        <?php endif; ?>
    </dt>
<?php endif; ?>

<dd class="element-body">
    <?php echo $element->render($params); ?>
</dd>
