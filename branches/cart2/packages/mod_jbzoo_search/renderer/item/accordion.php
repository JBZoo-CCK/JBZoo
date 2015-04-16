<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->jqueryAccordion();

?>


<div class="jbfilter-accordion jsAccordion">
    <?php
    for ($i = 1; $i <= 10; $i++) {
        if ($this->checkPosition('tab-' . $i)) {
            echo $this->renderPosition('tab-' . $i, array(
                'moduleParams' => $params,
                'style'        => 'filter.tab',
            ));
        }
    } ?>

</div>
