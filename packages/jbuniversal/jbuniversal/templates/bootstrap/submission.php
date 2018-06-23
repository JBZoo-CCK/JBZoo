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

$this->app->jbdoc->noindex();

// add page title
$page_title = sprintf(($this->item->id ? JText::_('Edit %s') : JText::_('Add %s')), '');
$this->app->document->setTitle($page_title);

$css_class = $this->application->getGroup() . '-' . $this->template->name;

$class = array('zoo', 'jbzoo', 'yoo-zoo', $css_class, $css_class . '-' . $this->submission->alias);

$this->app->jbassets->less(array(
    'jbassets:less/general/submission.less',
    'jbassets:less/general/_submission.less',
));

?>

<div id="yoo-zoo" class="<?php echo implode(' ', $class); ?>">

    <div class="submission">

        <h1 class="headline"><?php echo $page_title; ?></h1>

        <?php echo $this->partial('submission'); ?>

    </div>

</div>
