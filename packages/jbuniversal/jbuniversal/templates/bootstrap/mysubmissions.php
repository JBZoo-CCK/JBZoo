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

$this->app->jbdoc->noindex();

$appParams = $this->app->zoo->getApplication()->params;

$this->app->jblayout->setView($this);

$this->app->jbwrapper->start();

if ((int)$appParams->get('global.jbzoo_cart_config.enable', 0)) {

    $user = JFactory::getUser();
    if ($user->id) {
        ?>
        <div class="myorders">
            <h1><?php echo JText::_('JBZOO_MYORDERS_TITLE'); ?></h1>

            <p><?php echo JText::_('JBZOO_MYORDERS_DESCRIPTION'); ?>:</p>
            <?php echo $this->app->jblayout->render('myorders', $this->items); ?>
        </div>

    <?php
    } else {
        $url = $this->app->jbrouter->auth();
        JFactory::getApplication()->redirect($url, JText::_('JBZOO_AUTH_PLEASE'));
    } ?>


<?php } else { ?>

    <div class="mysubmissions">
        <h1 class="headline"><?php echo JText::_('My Submissions'); ?></h1>

        <p><?php echo sprintf(JText::_('Hi %s, here you can edit your submissions and add new submission.'), $this->user->name); ?></p>
        <?php echo $this->partial('mysubmissions'); ?>
    </div>

<?php
}

$this->app->jbwrapper->end();
