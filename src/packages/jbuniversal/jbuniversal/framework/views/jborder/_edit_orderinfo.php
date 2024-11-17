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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\String\StringHelper;
// add check admin position
$html   = $this->orderFieldRender->renderAdminEdit(array('order' => $order));
$isShow = StringHelper::trim(strip_tags($html));

?>

<?php if ($isShow) : ?>
    <div class="uk-panel">
        <h2><?php echo JText::_('JBZOO_ORDER_USERINFO'); ?></h2>
        <dl class="uk-description-list-horizontal"><?php echo $html; ?></dl>
    </div>
<?php endif; ?>
