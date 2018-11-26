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
 * Class JBCartElementOrder
 */
abstract class JBCartElementOrder extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_ORDER;

    /**
     * Render shipping in order
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
            ));
        }

        return false;
    }

    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $value = $this->getUserState($params->get('user_field'));

        return $this->app->html->_(
            'control.text',
            $this->getControlName('value'),
            $this->get('value', $value),
            'size="60" maxlength="255" id="' . $this->htmlId() . '"'
        );
    }

    /**
     * Get value from user profile
     * @param         $key
     * @param  string $default
     * @return mixed
     */
    public function getUserState($key, $default = '')
    {
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php'); 
        
        $user = JFactory::getUser();

        $context = 'com_users.user';
        $fieldsarr = array();
        $poleuser = $user->id;
        $poleuserstd = new \stdClass;
        $poleuserstd->id = $poleuser;
        $bigProfile = FieldsHelper::getFields($context, $poleuserstd, false);
        $properties = array_keys($this->app->jbuser->getFields());
        $whiteList = [$user->name, $user->username, $user->email, $user->registerDate, $user->lastvisitDate];
        $list = (array)array_intersect($whiteList, $properties);
        $list = array_combine($list, $list);

        foreach ($bigProfile as $poleProfile) {
            if (!empty($poleProfile->name)) {
                $fieldsarr[] = trim($poleProfile->value);
            }
        }
        
        $resultList = array_merge($whiteList,$fieldsarr);

        if (empty($default)) {
            $default = $this->config->get('default');
        }

        if ($user->guest) {
            return $default;
        }

        $value = null;

        if (!empty($resultList[$key])) {
            $value = $resultList[$key];
            if (empty($value) || !isset($value)) {
                $value = $default;
            }
        }
        
        return $value;
    }
}

/**
 * Class JBCartElementOrderException
 */
class JBCartElementOrderException extends JBCartElementException
{
}
