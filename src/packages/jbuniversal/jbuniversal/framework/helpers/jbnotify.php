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
 * Class JBNotifyHelper
 */
class JBNotifyHelper extends AppHelper
{
    /**
     * Show warning message
     * @param $message string
     * @return mixed
     */
    public function warning($message)
    {
        $this->_callType($message, 'raiseWarning');
    }

    /**
     * Show notice message
     * @param $message string
     * @return mixed
     */
    public function notice($message)
    {
        $this->_callType($message, 'raiseNotice');
    }

    /**
     * Show error message
     * @param $message
     * @return mixed
     */
    public function error($message)
    {
        $this->_callType($message, 'raiseError');
    }

    /**
     * @param $messages
     * @param string $messageType
     * @return mixed
     */
    protected function _callType($messages, $messageType = 'raiseNotice')
    {
        if (is_array($messages)) {
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    $this->_callType($message, $messageType);
                }
            }

        } else {
            $this->_call(array($this->app->error, $messageType), array(0, JText::_($messages)));
        }

    }

}
