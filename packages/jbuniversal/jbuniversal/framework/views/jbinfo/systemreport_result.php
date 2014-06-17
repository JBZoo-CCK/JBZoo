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


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JBZoo application report</title>
    <link rel="stylesheet"
          href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css">

    <style type="text/css">
        body {
            overflow-y: scroll !important;
        }

        .nav-tabs>li {
            margin-bottom: 0
        }
    </style>
</head>
<body>

<div id="wrap">
    <div class="container">
        <div class="page-header">
            <h1>JBZoo Application System Report</h1>
        </div>
        <ul class="nav nav-tabs" id="tabs">
            <li class="active"><a href="#tabs-versions" data-toggle="tab">Versions & OS</a></li>
            <li><a href="#tabs-zooreq" data-toggle="tab">Zoo checker</a></li>
            <li><a href="#tabs-fsmodzoo" data-toggle="tab">FS Modified (Zoo)</a></li>
            <li><a href="#tabs-fsmodjbzoo" data-toggle="tab">FS Modified (JBZoo)</a></li>
            <li><a href="#tabs-fspaths" data-toggle="tab">FS Main Paths</a></li>
            <li><a href="#tabs-fsperms" data-toggle="tab">FS Permissions</a></li>
            <li><a href="#tabs-sef" data-toggle="tab">Joomla config</a></li>
            <li><a href="#tabs-phpmain" data-toggle="tab">PHP main</a></li>
            <li><a href="#tabs-phpinfo" data-toggle="tab">phpInfo()</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tabs-versions">
                <?php
                $jversion = new JVersion();
                $app = App::getInstance('zoo');
                ?>
                <p>Generated: <?php echo date(DATE_W3C, time()); ?></p>
                <ul>
                    <li><strong>Joomla (long name)</strong>: <?php echo $jversion->getLongVersion(); ?></li>
                    <li><strong>Zoo</strong>: <?php echo $app->jbversion->zoo(); ?></li>
                    <li><strong>JBZoo</strong>: <?php echo $app->jbversion->jbzoo(); ?></li>
                    <li><strong>Widgetkit</strong>: <?php
                        $wkVersion = $app->jbversion->widgetkit();
                        if ($wkVersion) {
                            echo $wkVersion;
                            echo ' ' . $app->jbenv->isWidgetkit(true) ? 'free' : 'full';
                        } else {
                            echo 'No install';
                        }
                        ?>
                    </li>
                    <li><strong>PHP</strong>: <?php echo phpversion(); ?></li>
                    <li><strong>MySQL</strong>: <?php echo JFactory::getDbo()->getVersion(); ?></li>
                    <li><strong>PHP OS</strong>: <?php echo PHP_OS; ?></li>
                </ul>

            </div>

            <div class="tab-pane" id="tabs-fsmodjbzoo">
                <?php $results = $this->app->jbcheckfiles->check(); ?>
                <div class="creation-form">
                    <?php if (empty($results)) : ?>
                        <div class="infobox"><?php echo JText::_('JBZOO_MODIFICATIONS_NOT_FOUND'); ?></div>
                    <?php else: ?>
                        <?php foreach ($results as $type => $result) : ?>
                            <div class="importbox">
                                <div>
                                    <h3><?php echo $type; ?>:</h3>
                                    <ul class="<?php echo $type; ?>">
                                        <?php foreach ($result as $file) : ?>
                                            <li><?php echo $file; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane" id="tabs-fsmodzoo">
                <?php $results = $this->app->modification->check(); ?>
                <div class="creation-form">
                    <?php if (empty($results)) : ?>
                        <div class="infobox"><?php echo JText::_('JBZOO_MODIFICATIONS_NOT_FOUND'); ?></div>
                    <?php else: ?>
                        <?php foreach ($results as $type => $result) : ?>
                            <div class="importbox">
                                <div>
                                    <h3><?php echo $type; ?>:</h3>
                                    <ul class="<?php echo $type; ?>">
                                        <?php foreach ($result as $file) : ?>
                                            <li><?php echo $file; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane" id="tabs-fspaths">
                <?php

                $paths = array(
                    'root'          => JPATH_ROOT,
                    'tmp'           => $this->app->jbpath->sysPath('tmp'),
                    'cache'         => $this->app->jbpath->sysPath('cache'),
                    'cache/com_zoo' => $this->app->jbpath->sysPath('tmp', 'com_zoo'),
                    'cache/jbzoo'   => $this->app->jbpath->sysPath('tmp', 'jbzoo'),
                    'jbuniversal'   => $this->app->path->path('jbapp:'),
                );

                foreach ($paths as $key => $path) {
                    echo '<h5>' . $key . '</h5>';
                    echo '<pre>', print_r($this->app->jbpath->getInfo($path), true), '</pre>';
                }
                ?>
            </div>

            <div class="tab-pane" id="tabs-fsperms">
                <?php if (IS_UNIX && function_exists('exec')) {
                    echo '<hr />';
                    echo '<h5>Linux file list</h5>';
                    echo '<pre>', system('ls ' . realpath(JPATH_ROOT) . ' -la'), '</pre>';
                    echo '<h5>User ID</h5>';
                    echo '<pre>', exec('id'), '</pre>';
                } else {
                    echo 'Only for Unix-like system';
                }
                ?>
            </div>

            <div class="tab-pane" id="tabs-sef">
                <?php
                $config = (array)(new JConfig());

                // security
                $exclude = array('user', 'password', 'db', 'dbprefix', 'secret', 'ftp_host', 'ftp_port', 'ftp_user',
                    'ftp_pass', 'ftp_root', 'smtpuser', 'smtppass', 'smtphost', 'smtpsecure', 'smtpport');

                foreach ($config as $key => $value) {
                    if (in_array($key, $exclude)) {
                        $config[$key] = '***** hidden *****';
                    }
                }

                ksort($config);
                echo '<pre>', print_r($config, true), '</pre>';
                ?>
            </div>

            <div class="tab-pane" id="tabs-zooreq">
                <?php
                $this->app->loader->register('AppRequirements', 'installation:requirements.php');

                $requirements = $this->app->object->create('AppRequirements');
                $requirements->checkRequirements();
                $requirements->displayResults();
                ?>
            </div>

            <div class="tab-pane" id="tabs-phpmain">
                <?php
                $data = array(
                    'display_errors'                => ini_get('display_errors'),
                    'display_startup_errors'        => ini_get('display_startup_errors'),
                    'error_log'                     => ini_get('error_log'),
                    'log_errors'                    => ini_get('log_errors'),
                    'error_reporting'               => ini_get('error_reporting'),

                    'memory_limit'                  => ini_get('memory_limit'),
                    'max_execution_time'            => ini_get('max_execution_time'),
                    'max_input_vars'                => ini_get('max_input_vars'),
                    'realpath_cache_size'           => ini_get('realpath_cache_size'),
                    'open_basedir'                  => ini_get('open_basedir'),

                    'apc.enabled'                   => ini_get('apc.enabled'),
                    'apc.shm_size'                  => ini_get('apc.shm_size'),

                    'short_open_tag'                => ini_get('short_open_tag'),
                    'allow_url_fopen'               => ini_get('allow_url_fopen'),
                    'date.timezone'                 => ini_get('date.timezone'),
                    'default_charset'               => ini_get('default_charset'),
                    'disable_classes'               => ini_get('disable_classes'),
                    'disable_functions'             => ini_get('disable_functions'),

                    'mbstring.encoding_translation' => ini_get('mbstring.encoding_translation'),
                    'mbstring.func_overload'        => ini_get('mbstring.func_overload'),
                    'mbstring.internal_encoding'    => ini_get('mbstring.internal_encoding'),
                    'mbstring.language'             => ini_get('mbstring.language'),

                    'safe_mode'                     => ini_get('safe_mode'),
                    'safe_mode_exec_dir'            => ini_get('safe_mode_exec_dir'),
                    'safe_mode_include_dir'         => ini_get('safe_mode_include_dir'),
                    'safe_mode_protected_env_vars'  => ini_get('safe_mode_protected_env_vars'),
                );

                echo '<pre>', print_r($data, true), '</pre>';
                ?>
            </div>

            <div class="tab-pane" id="tabs-phpinfo">
                <p><?php phpinfo(); ?></p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://yandex.st/bootstrap/2.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>
</body>
</html>
