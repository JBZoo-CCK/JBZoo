<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
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