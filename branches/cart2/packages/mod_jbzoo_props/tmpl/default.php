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


$zoo = App::getInstance('zoo');

// init assets
$zoo->jbassets->filterprops($itemLayout);
?>

<div class="jbzoo jbzoo-props props-list-<?php echo $itemLayout; ?>">
    <?php echo $renderer->render('item.' . $itemLayout, array(
        'type'        => $type,
        'layout'      => $itemLayout,
        'application' => $application,
        'params'      => $params,
        'module'      => $module
    )); ?>
</div>
