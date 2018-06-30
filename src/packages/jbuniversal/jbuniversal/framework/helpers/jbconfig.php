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

/**
 * Class JBConfigHelper
 */
class JBConfigHelper extends AppHelper
{
    /**
     * @var array
     */
    protected $_oldVersionMap = [
        // custom config
        'JBZOO_CONFIG_SHOWUPDATE'         => 'update_show',
        'JBZOO_CONFIG_CURRENCY_ONLINE'    => 'currency_mode',
        'JBZOO_CONFIG_ADMINMENU'          => 'adminmenu_show',
        'JBZOO_CONFIG_DELETE_PRICE_LABEL' => 'delpricelbl_mode',

        // yml config
        'JBZOO_CONFIG_YML_SITE_URL'       => 'site_url',
        'JBZOO_CONFIG_YML_APP_LIST'       => 'app_list',
        'JBZOO_CONFIG_YML_SITE_NAME'      => 'site_name',
        'JBZOO_CONFIG_YML_COMPANY_NAME'   => 'company_name',
        'JBZOO_CONFIG_YML_TYPE'           => 'type_list',
        'JBZOO_CONFIG_YML_CURRENCY_RATE'  => 'currency_rate',
        'JBZOO_CONFIG_YML_FILE_PATH'      => 'file_path',
        'JBZOO_CONFIG_YML_FILE_NAME'      => 'file_name',
    ];

    /**
     * Compatibility list of params were saved in config files
     * @param  string|null $group
     * @return array|object
     */
    public function getList($group = null)
    {
        $ymlConfig = $this->app->path->path('jbapp:config') . '/yml_config.php';
        if (JFile::exists($ymlConfig)) {
            require_once $ymlConfig;
        }

        $config = $this->app->path->path('jbapp:config') . '/config.php';
        if (JFile::exists($config)) {
            require_once $config;
        }

        $result = [];
        foreach ($this->_oldVersionMap as $key => $value) {
            if (!defined($key)) {
                continue;
            }

            if (in_array($key, ['JBZOO_CONFIG_YML_TYPE', 'JBZOO_CONFIG_YML_APP_LIST'])) {
                $result[$value] = explode(':', constant($key));
            } else {
                $result[$value] = constant($key);
            }
        }

        if (!empty($group)) {
            $result = JBModelConfig::model()->getGroup($group, $result);
        }

        return $this->app->data->create($result);
    }

    /**
     * Save file
     * @param array $params
     * @param       $path
     * @return bool
     */
    public function saveToFile(array $params, $path)
    {
        $fileTemplate = [
            '<?php',
            '',
            '// no direct access',
            'defined(\'_JEXEC\') or die(\'Restricted access\');',
            '',
            '',
        ];

        foreach ($params as $key => $value) {

            $constName = JString::strtoupper($key);
            $value = str_replace('\'', "\\'", $value);

            $fileTemplate[] = 'define(\'' . $constName . '\', \'' . $value . '\');';
        }

        $fileTemplate[] = '';

        $fileContent = implode(PHP_EOL, $fileTemplate);

        if (JFile::exists($path)) {
            JFile::delete($path);
        }

        if (!JFile::write($path, $fileContent)) {
            $this->app->jbnotify->warning('The file is not created, check file permissions for JBZoo directory');

            return false;
        }

        return true;
    }

}
