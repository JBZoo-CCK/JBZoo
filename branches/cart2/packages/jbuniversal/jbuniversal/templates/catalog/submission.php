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

// add page title
$page_title = sprintf(($this->item->id ? JText::_('Edit %s') : JText::_('Add %s')), '');
$this->app->document->setTitle($page_title);

$css_class = $this->application->getGroup() . '-' . $this->template->name;

$class = array('zoo', 'jbzoo', 'yoo-zoo', $css_class, $css_class . '-' . $this->submission->alias);

$this->app->jbassets->less(array(
    'jbassets:less/general/submission.less',
));

?>

<div id="yoo-zoo" class="<?php echo implode(' ', $class); ?>">

    <div class="submission">

        <h1 class="headline"><?php echo $page_title; ?></h1>

        <?php echo $this->partial('submission'); ?>

    </div>

</div>
