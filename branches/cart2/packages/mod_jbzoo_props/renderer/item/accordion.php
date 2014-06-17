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


App::getInstance('zoo')->jbassets->jqueryAccordion();

?>

<div id="<?php echo uniqid('accordion-'); ?>" class="filter-props jsAccordion">
    <?php
    for ($i = 1; $i <= 10; $i++) {
        echo $this->renderPosition('tab-' . $i, array(
                'style' => 'filterprops.tab',
            )
        );
    }
    ?>
</div>
