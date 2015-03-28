<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if ($this->get('ordersList')) :

    $url = $this->app->jbrouter->admin(array(
            'task'   => 'emailPreview',
            'layout' => $this->layout
        )
    ); ?>

    <div class="email-preview" data-uk-dropdown="">

        <a href="" class="jsEmailTmplPreview" title="Preview">
            <i class="uk-icon-photo uk-icon-medium"></i>
        </a>

        <div class="uk-dropdown uk-dropdown-small">
            <ul id="jsOrderList" class="uk-nav uk-nav-dropdown order-list">
                <?php foreach ($this->get('ordersList') as $id => $name) : ?>
                    <li>
                        <a class="order-id" href="" data-id="<?php echo $id; ?>">
                            <?php echo $name; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

    <?php echo $this->app->jbassets->widget('.email-preview', 'JBZooEmailPreview', array('url' => $url), true); ?>

<?php endif;
