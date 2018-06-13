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
