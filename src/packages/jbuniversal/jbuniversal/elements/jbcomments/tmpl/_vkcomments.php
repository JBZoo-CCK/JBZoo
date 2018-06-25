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

?>

<div id="<?php echo $vkParams['vkId'] ;?>"></div>

<script type="text/javascript">
    jQuery(function ($) {
        VK.init(<?php echo json_encode($vkParams['vkInit']); ?>);
        VK.Widgets.Comments('<?php echo $vkParams['vkId'] ;?>',
            <?php echo json_encode($vkParams['vkParams']); ?>,
            <?php echo '"'.$vkParams['vkPageUrl'].'"' ;?>);
    });
</script>
