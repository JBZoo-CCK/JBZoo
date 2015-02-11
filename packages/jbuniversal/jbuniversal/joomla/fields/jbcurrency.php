<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBCurrency
 */
class JFormFieldJBCurrency extends JFormField
{

    protected $type = 'jbcurrency';

    /**
     * @return string
     */
    public function getInput()
    {
        // get app
        $app = App::getInstance('zoo');

        $currencyList = $app->jbmoney->getCurrencyList();
        if (isset($currencyList['%'])) {
            unset($currencyList['%']);
        }

        $isMultiply  = (int)$this->element->attributes()->multiple;
        $defaultCode = (int)$this->element->attributes()->defaultCode;

        // create select
        $options = array();

        if (!$defaultCode) {
            unset($currencyList[JBCartValue::DEFAULT_CODE]);
        }

        foreach ($currencyList as $key => $currency) {
            $options[] = $app->html->_('select.option', $key, $currency);
        }

        $attrs = $isMultiply ? 'multiple="multiple" size="5"' : '';

        return $app->html->_(
            'select.genericlist',
            $options,
            $this->getName($this->fieldname),
            $attrs,
            'value',
            'text',
            $this->value
        );
    }

}
