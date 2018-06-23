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

// assets
$this->app->jbassets->less('jbassets:less/general.less');
$this->app->jbassets->initTooltip();

$isShowlabel = (int)$params->get('showlabel');
if ($isShowlabel) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
}

$attrs = array(
    'class' => array(
        'element-' . $element->identifier,
        'element-' . $element->getElementType(),
        $params['first'] ? 'first' : '',
        $params['last'] ? 'last' : '',
    )
);

?>

<tr <?php echo $this->app->jbhtml->buildAttrs($attrs); ?>>

    <?php if ($isShowlabel) : ?>
        <td class="element-label">
            <?php echo $label; ?>

            <?php if ($description = $element->getDescription()) : ?>
                <span class="jbtooltip" title="<?php echo $description; ?>"> </span>
            <?php endif; ?>

        </td>
    <?php endif; ?>

    <td class="element-body" <?php if (!$isShowlabel) : ?>colspan="2"<?php endif; ?>>
        <?php echo $element->render($params); ?>
    </td>
</tr>
