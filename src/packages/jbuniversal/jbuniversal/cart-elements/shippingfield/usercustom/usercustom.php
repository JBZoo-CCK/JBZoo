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
 * Class JBCartElementShippingFieldUserCustom
 */
class JBCartElementShippingFieldUserCustom extends JBCartElementShippingField
{
    /**
     * @var array
     */
    // protected $_customFields = array(
    //     'postal_code', 'city', 'region', 'street', 'house', 'apartament', 'phone'
    // );

    /**
     * Element constructor.
     *
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);
        $this->_loadLangs();
    }

    /**
     * Render submission.
     *
     * @param array $params
     * @return bool|mixed|string
     */
    public function renderSubmission($params = array())
    {
        $tmpl = $this->config->get('tmpl', 'text');

        if ($layout = $this->getLayout('submission_' . $tmpl . '.php')) {

            $user = JFactory::getUser();

            return self::renderLayout($layout, array(
                'params' => $params,
                'user'   => $user,
            ));
        }

        return false;
    }

    /**
     * @return AppParameterForm
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__) . '/fields');
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php'); 
        $context = 'com_users.user';
        $fieldsarr = array();
        $poleuser = JFactory::getUser()->id;
        $poleuserstd = new \stdClass;
        $poleuserstd->id = $poleuser;
        $bigProfile = FieldsHelper::getFields($context, $poleuserstd, false);

        foreach ($bigProfile as $poleProfile) {
            if (!empty($poleProfile->name)) {
                $fieldsarr[] = trim($poleProfile->name);
            }
        }

        return $fieldsarr;
    }

}
