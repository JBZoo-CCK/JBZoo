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


/**
 * Class JBUpdateHelper
 */
class JBUpdateHelper extends AppHelper
{

    static $isMessageShow = false;

    /**
     * Check for update JBZoo
     * @return array
     */
    public function checkNewVersion()
    {
        $curApp = $this->app->zoo->getApplication();
        if ($curApp->getGroup() == JBZOO_APP_GROUP) {

            $response = $this->app->data->create($curApp->checkupd(true));
            $params   = $this->app->jbconfig->getList('config.custom');

            if (
                $params->get('update_show', 1) &&
                $response->get('update_message') &&
                $response->get('version_last') &&
                version_compare($response->get('version_last'), JBUniversalApplication::JBZOO_VERSION) == 1
            ) {
                $this->_showMessage($response['update_message']);
            }
        }
    }

    /**
     * Show update message
     */
    protected function _showMessage($message)
    {
        if (
            $this->app->jbrequest->is('option', 'com_zoo')
            &&
            !(preg_match('#^jb#', $this->app->jbrequest->getCtrl())
                || self::$isMessageShow
                || $this->app->jbrequest->isAjax()
            )
        ) {
            $this->app->jbnotify->notice($message);
            self::$isMessageShow = true;
        }

    }

}
