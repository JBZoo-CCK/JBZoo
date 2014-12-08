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
 * Class ElementDate
 * The date element class
 */
class ElementDate extends ElementRepeatable implements iRepeatSubmittable
{

    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

    /**
     * Get repeatable elements search data.
     * @return null
     */
    protected function _getSearchData()
    {
        return $this->get('value');
    }

    /**
     * Renders the repeatable element.
     * @param array $params
     * @return string
     */
    protected function _render($params = array())
    {
        $params = $this->app->data->create($params);
        return $this->app->html->_('date', $this->get('value', ''), $this->app->date->format($params->get('date_format') == 'custom' ? $params->get('custom_format') : $params->get('date_format')));
    }

    /**
     * Renders the repeatable edit form field.
     * @return mixed
     */
    protected function _edit()
    {
        $name = $this->getControlName('value');
        if ($value = $this->get('value', '')) {
            try {

                $value = $this->app->html->_('date', $value, $this->app->date->format(self::EDIT_DATE_FORMAT), $this->app->date->getOffset());

            } catch (Exception $e) {
            }
        }
        return $this->app->html->_('zoo.calendar', $value, $name, $name, array('class' => 'calendar-element'), true);
    }

    /**
     * Renders the element in submission.
     * @param array $params
     * @return mixed|void
     */
    public function _renderSubmission($params = array())
    {
        return $this->_edit();
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function _validateSubmission($value, $params)
    {

        $value = $value->get('value');
        if (!empty($value) && ($time = strtotime($value))) {
            $value = strftime(self::EDIT_DATE_FORMAT, $time);
        }

        return array('value' => $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
                ->addOption('date_format', self::EDIT_DATE_FORMAT)
                ->clean($value));
    }

    /**
     * Set data through data array.
     * @param array $data
     */
    public function bindData($data = array())
    {
        parent::bindData($data);

        foreach ($this as $self) {

            $value = $this->get('value', '');

            if (!empty($value) && ($value = strtotime($value)) && ($value = strftime(self::EDIT_DATE_FORMAT, $value))) {
                $tzoffset = $this->app->date->getOffset();
                $date     = $this->app->date->create($value, $tzoffset);
                $value    = $date->toSQL();
                $this->set('value', $value);
            }
        }

    }

    /**
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $this->app->jbassets->calendar();
        return parent::renderSubmission($params);
    }

}
