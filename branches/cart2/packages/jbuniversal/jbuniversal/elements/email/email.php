<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementRepeatable class
App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');

/**
 * Class ElementEmail
 * The email element class
 */
class ElementEmail extends ElementRepeatable implements iRepeatSubmittable
{

    /**
     * Checks if the repeatables element's value is set.
     * @param array $params
     * @return bool|int
     */
    protected function _hasValue($params = array())
    {
        $value = $this->get('value');
        return $this->_containsEmail($value);
    }

    /**
     * Gets the email text.
     * @return mixed
     */
    public function getText()
    {
        $text = $this->get('text', '');
        return empty($text) ? $this->get('value', '') : $text;
    }

    /**
     * Renders the repeatable element.
     * @param array $params
     * @return string
     */
    protected function _render($params = array())
    {
        // init vars
        $mode    = $this->_containsEmail($this->getText());
        $subject = $this->get('subject', '');
        $subject = !empty($subject) ? 'subject=' . $subject : '';
        $body    = $this->get('body', '');
        $body    = !empty($body) ? 'body=' . $body : '';
        $mailto  = $this->get('value', '');

        if ($subject && $body) {
            $mailto .= '?' . $subject . '&' . $body;
        } elseif ($subject || $body) {
            $mailto .= '?' . $subject . $body;
        }

        // JBZoo hack. Email link no render in emails
        return '<a href="mailto:' . $mailto . '" title="Mailto ' . $this->getText() . '">' . $this->getText() . '</a>';
    }

    /**
     * Renders the repeatable edit form field.
     * @return string
     */
    protected function _edit()
    {
        return $this->_editForm();
    }

    /**
     * Checks for an email address in a text.
     * @param $text
     * @return int
     */
    protected function _containsEmail($text)
    {
        return preg_match('/[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}/i', $text);
    }

    /**
     * Renders the element in submission.
     * @param array $params
     * @return string|void
     */
    public function _renderSubmission($params = array())
    {
        return $this->_editForm($params->get('trusted_mode'));
    }

    /**
     * @param bool $trusted_mode
     * @return string
     */
    protected function _editForm($trusted_mode = true)
    {
        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout,
                array('trusted_mode' => $trusted_mode
                )
            );
        }
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function _validateSubmission($value, $params)
    {
        $values = $value;

        $validator = $this->app->validator->create('string', array('required' => false));
        $text      = $validator->clean($values->get('text'));
        $subject   = $validator->clean($values->get('subject'));
        $body      = $validator->clean($values->get('body'));

        $value = $this->app->validator
            ->create('email', array('required' => $params->get('required')), array('required' => 'Please enter an email address.'))
            ->clean($values->get('value'));

        return compact('value', 'text', 'subject', 'body');
    }

}
