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

$currency = $params->get('currency_default', 'eur');
$target = $params->get('switcher_target', '.jbzoo');
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
