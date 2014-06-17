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
 * Class JBFormatHelper
 */
class JBFormatHelper extends AppHelper
{

    /**
     * Get currency from element config
     * @param $value
     * @param $params
     * @return mixed
     */
    public function number($value, array $params = array())
    {
        $formatParams = array();

        if (!isset($params['format'])) {
            $params['format'] = 2;
        }

        if ($params['format'] == 1) {
            $formatParams = array(
                'decimals'      => 2,
                'dec_point'     => '.',
                'thousands_sep' => ' ',
            );

        } elseif ($params['format'] == 2) {
            $formatParams = array(
                'decimals'      => 2,
                'dec_point'     => ',',
                'thousands_sep' => ' ',
            );

        } elseif ($params['format'] == 3) {
            $formatParams = array(
                'decimals'      => 2,
                'dec_point'     => '.',
                'thousands_sep' => '',
            );

        } elseif ($params['format'] == 4) {
            $formatParams = array(
                'decimals'      => 2,
                'dec_point'     => ',',
                'thousands_sep' => '',
            );

        } else {
            $formatParams = array(
                'decimals'      => $params['decimals'],
                'dec_point'     => $params['dec_point'],
                'thousands_sep' => $params['thousands_sep'],
            );
        }

        $value = $this->clearValue($value);

        $formatData = $this->app->data->create($formatParams);

        $value = number_format(
            $value,
            (int)$formatData->get('decimals', 2),
            $formatData->get('dec_point', ','),
            $formatData->get('thousands_sep', ' ')
        );

        return $value;
    }

    /**
     * Clear price string
     * @param $value
     * @return mixed|string
     */
    public function clearValue($value)
    {
        $value = trim($value);
        $value = preg_replace('#[^0-9\,\.]#ius', '', $value);
        $value = (float)str_replace(',', '.', $value);

        return $value;
    }

}
