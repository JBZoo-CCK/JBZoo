<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


?>
<div class="jbzoo">

    <p><?php echo JText::_('JBZOO_URL_ALL_DESC'); ?></p>

    <hr>

    <h3>Result URL (Status URL)</h3>

    <p><?php echo JText::_('JBZOO_URL_RESULT_DESC'); ?></p>
    <textarea rows="3" cols="70" readonly="readonly"
              style="width:auto;height:auto;"><?php echo $this->resultUrl; ?></textarea>

    <h3>Success URL</h3>

    <p><?php echo JText::_('JBZOO_URL_SUCCESS_DESC'); ?></p>
    <textarea rows="3" cols="70" readonly="readonly"
              style="width:auto;height:auto;"><?php echo $this->successUrl; ?></textarea>

    <h3>Fail URL</h3>

    <p><?php echo JText::_('JBZOO_URL_FAIL_DESC'); ?></p>
    <textarea rows="3" cols="70" readonly="readonly"
              style="width:auto;height:auto;"><?php echo $this->failUrl; ?></textarea>
</div>