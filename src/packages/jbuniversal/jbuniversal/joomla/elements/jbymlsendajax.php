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

$id    = uniqid();
$appId = App::getInstance('zoo')->zoo->getApplication()->id;
$link  = '/?controller=ymlexport&task=index&app_id=' . $appId . '&ajaxSubmit=true';

echo '<input id="' . $id . '" name="ajaxSubmit" type="button" class="ajaxSubmit"
 style="cursor: pointer" value="' . JText::_('JBZOO_FIELDS_YMLSENDAJAX') . '" />';

?>
<script type="text/javascript">
    (function ($) {
        $('#<?php echo $id ?>').on('click', function () {
            $.ajax({
                type    : 'GET',
                cache   : false,
                dataType: 'html',
                url     : '<?php echo $link ;?>',
                success : function () {
                    alert('<?php echo JText::_('JBZOO_YML_EXPORT') ?>');
                }
            });
        });
    })(jQuery);
</script>