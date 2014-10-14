<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$unique = $this->app->jbstring->getId('bool-');

if ((int)$this->getValue('value')) : ?>
    <div class="jbprice-bool" id="<?php echo $unique; ?>">
        <img src="<?php echo $this->app->jbimage->getUrl($params->get('image')); ?>"/>
    </div>
<?php endif;