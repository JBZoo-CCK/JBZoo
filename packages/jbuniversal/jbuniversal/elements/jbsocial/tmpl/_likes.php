<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$isShow =
    !empty($fbOption) ||
    !empty($twOption) ||
    !empty($okOption) ||
    !empty($gpOption) ||
    !empty($liOption) ||
    !empty($vkOption);

if ($isShow):?>
    <div class="jbzoo-likes">

        <!-- Render FB button -->
        <?php if (isset($fbOption['fbEnabled'])) : ?>
            <div class="jbzoo-like jbzoo-like-fb">
                <div id="<?php echo $fbOption['fbId'] ?>"></div>
                <script>
                    jQuery(function ($) {
                        setTimeout(function () {
                            (function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s);
                                js.id = id;
                                js.src = "<?php echo $fbOption['data-src'] ?>";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));
                        }, 3000);
                    });
                </script>
                <div <?php echo $this->app->jbhtml->buildAttrs($fbOption['params']) ?>></div>
            </div>
        <?php endif ?>

        <!-- Render TW button -->
        <?php if (isset($twOption['twEnabled'])) : ?>
            <div class="jbzoo-like jbzoo-like-tw">
                <a <?php echo $this->app->jbhtml->buildAttrs($twOption['params']) ?>><?php echo JText::_('JBZOO_LIKE_TW') ?></a>
                <script>
                    jQuery(function ($) {
                        setTimeout(function () {
                            !function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = "http://platform.twitter.com/widgets.js";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, "script", "twitter-wjs");
                        }, 3000);
                    });
                </script>
            </div>
        <?php endif ?>

        <!-- Render OK button -->
        <?php if (isset($okOption['okEnabled'])) : ?>
            <div class="jbzoo-like jbzoo-like-ok">
                <div id="<?php echo $okOption['okId'] ?>"></div>
                <script>
                    setTimeout(function () {
                        !function (d, id, did, st) {
                            var js = d.createElement("script");
                            js.src = "http://connect.ok.ru/connect.js";
                            js.onload = js.onreadystatechange = function () {
                                if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                                    if (!this.executed) {
                                        this.executed = true;
                                        setTimeout(function () {
                                            OK.CONNECT.insertShareWidget(id, did, st);
                                        }, 0);
                                    }
                                }
                            };
                            d.documentElement.appendChild(js);
                        }(document, '<?php echo $okOption['okId'] ?>', '<?php echo $okOption['okUrl'] ?>',
                            '<?php echo json_encode($okOption['params']) ?>');
                    }, 3000);
                </script>
            </div>
        <?php endif ?>

        <!-- Render Google+ button -->
        <?php if (isset($gpOption['gpEnabled'])) : ?>
            <div class="jbzoo-like jbzoo-like-gp">
                <div <?php echo $this->app->jbhtml->buildAttrs($gpOption['params']) ?>></div>
                <script type="text/javascript">
                    setTimeout(function () {
                        window.___gcfg = <?php echo json_encode($gpOption['scriptParams']) ?>;
                        (function () {
                            var po = document.createElement('script');
                            po.type = 'text/javascript';
                            po.async = true;
                            po.src = 'https://apis.google.com/js/plusone.js';
                            var s = document.getElementsByTagName('script')[0];
                            s.parentNode.insertBefore(po, s);
                        })();
                    }, 3000);
                </script>
            </div>
        <?php endif ?>

        <!-- Render LI button -->
        <?php if (isset($liOption['liEnabled'])) : ?>
            <div class="jbzoo-like jbzoo-like-li">
                <script src="//platform.linkedin.com/in.js" type="text/javascript">
                    lang:
                    <?php echo $liOption['lang'] ?>
                </script>
                <script type="IN/Share" <?php echo $this->app->jbhtml->buildAttrs($liOption['params']) ?>></script>
            </div>
        <?php endif ?>

        <!-- Render VK button -->
        <?php if (isset($vkOption['vkEnabled']) && !empty($vkOption['id'])) : ?>
            <div class="jbzoo-like jbzoo-like-vk">
                <div id="<?php echo $vkOption['vkId'] ?>"></div>
                <script type="text/javascript">
                    jQuery(function ($) {
                        setTimeout(function () {
                            VK.init({apiId: <?php echo $vkOption['id'] ?>, onlyWidgets: true});
                            VK.Widgets.Like(
                                "<?php echo $vkOption['vkId'] ?>",
                                <?php echo json_encode($vkOption['params']);?>,
                                <?php echo $vkOption['pageId'] ?>
                            );
                        }, 3000);
                    });
                </script>
            </div>
        <?php endif ?>

    </div>
<?php endif;