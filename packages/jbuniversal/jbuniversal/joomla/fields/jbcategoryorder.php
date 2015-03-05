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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBItemOrder
 */
class JFormFieldJBCategoryOrder extends JFormField
{

    protected $type = 'jbcategoryorder';

    /**
     * Get input HTML
     * @return string
     */
    public function getInput()
    {
        $app    = App::getInstance('zoo');
        $params = $app->jborder->getCatOrderings(false);

        $app->jbassets->jQuery();
        $app->jbassets->js("jbapp:joomla/fields/jbcategoryorder.js");

        $orderId = uniqid('jbcategoryorder-');

        $value = array(
            'order'   => $this->value,
            'reverse' => 0,
            'random'  => 0,
        );


        if (!empty($this->value)) {
            if ($this->value == 'random' || $this->value == 'rrandom') {
                $value['random'] = 1;
            } else {
                if (preg_match('#^r#', $this->value)) {
                    $cleanValue = preg_replace('#^r#', '', $this->value);
                    if (isset($params[$cleanValue])) {
                        $value['reverse'] = 1;
                        $value['order']   = $cleanValue;
                    }
                } else {
                    $value['order'] = $this->value;
                }
            }
        }

        $html[] = '<div id="' . $orderId . '" style="float: left;width: 250px;">';

        $html[] = $app->jbhtml->select($params, "", array('class' => 'order-select'), $value['order']);

        $html[] = '<label class="order-reverse-wrap"><input class="order-reverse" type="checkbox">'
            . JText::_('JBZOO_ORDER_REVERSE') . '</label> ';

        $html[] = '<label class="order-random-wrap"><input class="order-random" type="checkbox">'
            . JText::_('JBZOO_ORDER_RANDOM') . '</label>';

        $html[] = $app->jbhtml->hidden($this->getName($this->fieldname), $this->value, array('class' => 'hidden-value'));
        $html[] = '</div>';

        $html[] = $app->jbassets->widget('#' . $orderId, 'JBCategoryOrder', $value, true);

        return implode(PHP_EOL, $html);
    }
}
