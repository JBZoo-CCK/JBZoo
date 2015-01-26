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
defined('_JEXEC') or die('Restricted access'); ?>

<td class="align-right"></td>
<td class="align-right">
    <?php echo $this->getName(); ?>
</td>
<td class="align-right">
    <?php echo $rate->htmlAdv($this->get('currency', $value->cur()), true); ?>
</td>
<td class="align-right">
    <small><?php echo $value->html(); ?></small>
</td>


