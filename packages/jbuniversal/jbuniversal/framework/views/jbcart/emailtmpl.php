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

$url = $this->app->jbrouter->admin(array(
        'task'   => 'getPreview',
        'format' => 'raw'
    )
);
?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_CART_' . $this->task); ?></h2>

        <div class="jsEmailPreview email-preview">
            <a href="" class="jsEmailTmplPreview">Preview</a>
            <ul id="jsOrderList" class="uk-nav uk-nav-side order-list">
                <?php foreach ($this->ordersList as $id => $name) : ?>
                    <li>
                        <a class="order-id" href="" data-id="<?php echo $id; ?>">
                            <?php echo $name; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php echo $this->partial('editpositions', array(
            'positions' => $this->positions,
            'groupList' => $this->groupList,
        ));?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $('.jsEmailPreview').JBZooEmailPreview({
            url: "<?php echo $url; ?>"
        });
    });
</script>