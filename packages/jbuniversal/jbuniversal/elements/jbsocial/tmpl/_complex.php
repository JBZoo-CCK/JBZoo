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

if (isset($yaOption['yaEnabled'])) : ?>

    <div class="jbzoo-share">
        <div id="<?php echo $yaOption['element']; ?>"></div>

        <script type="text/javascript">
            jQuery(function ($) {
                setTimeout(function () {
                    new Ya.share(<?php echo json_encode($yaOption); ?>);
                }, 3000);
            });
        </script>

    </div>

<?php endif ?>
