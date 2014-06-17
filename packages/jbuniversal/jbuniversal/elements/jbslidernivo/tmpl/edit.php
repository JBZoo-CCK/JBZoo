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


$id = 'elements[' . $element . ']';
?>

<div id="<?php echo $id; ?>">

    <div class="row">
        <?php echo $this->app->html->_(
        'control.selectdirectory', $directory, false, 'elements[' . $element . '][value]', $value
    ); ?>
    </div>

</div>