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
            $options[$key] = $currency;
        }

        $attrs = array();
        if ($isMultiply) {
            $attrs['multiple'] = 'multiple';
            $attrs['size']     = '5';
        }

        return $app->jbhtml->select($options, $this->getName($this->fieldname), $attrs, $this->value);
    }

}
