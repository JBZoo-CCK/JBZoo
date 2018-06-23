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
