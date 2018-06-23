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
 * Class JBCartElementEmail
 */
abstract class JBCartElementEmail extends JBCartElement
{
    /**
     * @type JMail
     */
    protected $_mailer;

    /**
     * @param JMail $mailer
     */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
     * Build default attributes or merge with needed
     * @param  array $attrs
     * @return mixed
     */
    public function getAttrs($attrs = [])
    {
        $default = [
            'align' => 'left',
        ];

        if (empty($attrs)) {
            return $this->app->jbhtml->buildAttrs($default);
        }

        $merged = array_merge($default, $attrs);

        return $this->app->jbhtml->buildAttrs($merged);
    }

    /**
     * Build default styles or merge with needed
     * @param  bool  $merge
     * @param  array $styles
     * @return mixed
     */
    public function getStyles($styles = [], $merge = false)
    {
        $default = [
            'text-align'    => 'left',
            'border-bottom' => '1px solid #dddddd',
            'font-size'     => '14px',
            'color'         => '#333'
        ];

        if (empty($styles)) {
            return $this->buildStyles($default);
        }

        if ($merge === true) {
            $styles = array_merge($default, $styles);
        }

        return $this->buildStyles($styles);
    }

    /**
     * Build styles from array
     * @param  $styles
     * @return string
     */
    public function buildStyles($styles)
    {
        $result = ' style="';

        if (is_string($styles)) {
            $result .= $styles;

        } elseif (!empty($styles)) {
            foreach ($styles as $key => $value) {

                if (!empty($value) || $value == '0' || $key == 'value') {
                    $result .= $key . ':' . $value . ';';
                }
            }
        }

        $result .= "\"";

        return JString::trim($result);
    }

    /**
     * @param         $text
     * @param  string $color - name|hex|rgb
     * @param  int    $size  - from 1 to 7
     * @return string
     */
    public function fontColor($text, $color = '#000', $size = 2)
    {
        return '<i><font size="' . $size . '" color="' . $color . '">' . $text . '</font></i>';
    }

}

/**
 * Class JBCartElementNotificationException
 */
class JBCartElementEmailNotificationException extends JBCartElementException
{
}