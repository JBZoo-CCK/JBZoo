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

$currency = $params->get('currency_default', 'eur');
$target   = $params->get('switcher_target', '.jbzoo');
?>

<script>
    jQuery(function ($) {
        $('.jsMoney', $('<?php echo $target;?>'))
            .filter(function () {
                return $(this).closest('.jsNoCurrencyToggle').length == 0 || $(this).is('.jsNoCurrencyToggle');
            })
            .JBZooMoney({}).JBZooMoney('convert', '<?php echo $currency;?>');
    });
</script>
