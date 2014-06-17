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
 * Class ElementJBCommentsRender
 */
class ElementJBCommentsRender extends Element implements iSubmittable
{
    /**
     * Has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Render action
     * @param array $params
     * @return mixed
     */
    public function render($params = array())
    {
        $view = $this->app->jblayout->getView();

        if ($view) {
            if (!defined('JBZOO_COMMENTS_RENDERED_' . $this->getItem()->id)) {
                define('JBZOO_COMMENTS_RENDERED_' . $this->getItem()->id, true);
            }

            return $this->app->comment->renderComments($view, $this->getItem());
        }

        return null;
    }

    /**
     * Edit action
     * @return bool
     */
    function edit()
    {
        // no params for item
        return false;
    }

    /**
     * Validate submission
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        return array('value' => $value->get('value'));
    }

    /**
     * Render submition action
     * @param array $params
     * @return mixed
     */
    public function renderSubmission($params = array())
    {
        return $this->edit();
    }

}
