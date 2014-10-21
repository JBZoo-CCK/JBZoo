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


?>


<?php if ($view->formRenderer->checkPosition('fields')) : // TODO replace checking to correct way ?>
    <div class="jbzoo-basket-fields">

        <?php echo $view->formRenderer->render('basketform.default', array(
            'order' => $view->order,
        )); ?>

    </div>
<?php endif; ?>
