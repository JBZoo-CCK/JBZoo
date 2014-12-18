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
 * Class JBFieldHelper
 */
class JBFieldHelper extends AppHelper
{
    /**
     * Render currency list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function elementCode($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if (empty($value) && $parent->element) {
            $value = $parent->element->identifier;
        }

        $attrs = array(
            'name'         => $this->_getName($controlName, $name),
            'type'         => 'text',
            'value'        => $value,
            'autocomplete' => 'off',
        );

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render currency list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function currencyList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $currencyList = $this->app->jbmoney->getCurrencyList();
        $currencyList = $this->app->jbarray->unshiftAssoc($currencyList, JBCartValue::DEFAULT_CODE, JText::_('JBZOO_JBCURRENCY_DEFAULT_CUR'));

        unset($currencyList['%']);
        return $this->_renderList($currencyList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render application list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function applicationList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $appList    = $this->app->table->application->all();
        $basketOnly = (int)$this->_getAttr($node, 'cart_only', 0);

        $options = array();
        foreach ($appList as $app) {

            if ($basketOnly) {
                if ((int)$app->getParams()->get('global.jbzoo_cart_config.enable', 0)) {
                    $options[$app->id] = $app->name;
                }
            } else {
                $options[$app->id] = $app->name;
            }

        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render layout list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function layoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $layoutName = str_replace('layout_', '', $this->_getAttr($node, 'name', ''));

        $auto   = $this->_getAttr($node, 'auto', 'true');
        $path   = JPath::clean(
            $this->app->path->path('jbtmpl:') . '/' .
            $this->app->jbenv->getTemplateName()
            . '/renderer'
            . '/' . $layoutName
        );
        $system = $this->app->path->path("jbapp:templates-system/renderer/{$layoutName}");

        if ($auto == 'true') {
            $options = array('__auto__' => JText::_('JBZOO_LAYOUT_AUTOSELECT'));
        }

        if (JFolder::exists($path)) {
            $files = JFolder::files($path, '^([-_A-Za-z0-9\.]*)\.php$', false, false, array('.svn', 'CVS'));
            foreach ($files as $tmpl) {
                $tmpl           = basename($tmpl, '.php');
                $options[$tmpl] = $tmpl;
            }
        }

        if (JFolder::exists($system) && $system) {
            $files = JFolder::files($system, '^([-_A-Za-z0-9\.]*)\.php$', false, false, array('.svn', 'CVS'));
            foreach ($files as $tmpl) {
                $tmpl           = basename($tmpl, '.php');
                $options[$tmpl] = $tmpl;
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render submission layout list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function formLayoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = array(
            '' => JText::_('JBZOO_CART_SELECT_ORDER_FORM')
        );

        if ($application) {

            foreach ($application->getTypes() as $type) {

                $submissions = $this->app->type->layouts($type, 'submission');

                if (!empty($submissions)) {
                    foreach ($submissions as $submission) {
                        $options[$type->id . ':' . $submission->layout] = $type->name . ' / ' . $submission->name;
                    }
                }
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render email layout list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function emailLayoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = array(
            '' => JText::_('JBZOO_CART_SELECT_ORDER_FORM')
        );

        if ($application) {
            foreach ($application->getTypes() as $type) {

                $layouts = $this->app->type->layouts($type);

                if (!empty($layouts)) {
                    foreach ($layouts as $layout) {
                        if ($layout->get('layout')) {
                            $options[$layout->get('layout')] = $type->name . ' / ' . $layout->get('name');
                        }
                    }
                }
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render submission list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function submissionList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = array('' => JText::_('JBZOO_CART_SELECT_ORDER_FORM'));
        if ($application) {
            foreach ($application->getSubmissions() as $submission) {
                $options[$submission->id] = $submission->name;
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render layout list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function layoutListGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('layoutList', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render typelist list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function types($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $options = array();

        $application = $this->app->zoo->getApplication();
        if ($application) {
            foreach ($application->getTypes() as $type) {
                $options[$type->id] = $type->name;
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render hidden timestamp
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function timestamp($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $attrs = array(
            'name'  => $this->_getName($controlName, $name),
            'type'  => 'hidden',
            'value' => time(),
        );

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render hidden timestamp
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function currentCategoryId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($cid = (array)$this->app->jbrequest->get('cid')) {
            return current($cid);
        }

        return '<em>undefined</em>';
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param                  $parent
     *
     * @return string
     */
    public function currentApplicationId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();
        if (isset($application->id)) {
            return $application->id;
        }

        return '<em>undefined</em>';
    }

    /**
     * Render hidden timestamp
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function select($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $optionList = array();
        foreach ($node->children() as $option) {
            $optionList[$this->_getAttr($option, 'value', '')] = JText::_((string)$option);
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     *
     * @return mixed
     */
    public function singlechoice($name, $value, $controlName, SimpleXMLElement $node)
    {
        $optionList = array();
        foreach ($node->children() as $option) {
            $optionList[$this->_getAttr($option, 'value', '')] = JText::_((string)$option);
        }

        $options = $this->app->html->listOptions($optionList);

        $attributes['class'] = $this->_getAttr($node, 'class', 'inputbox');

        if (!empty($value)) {
            $attributes['readonly'] = 'readonly';
        }

        return $this->app->html->genericList($options, $this->_getName($controlName, $name), $attributes, 'value',
            'text', $value);
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return string $field
     */
    public function finder($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $request = $this->app->request;
        $html    = array();
        $id      = $this->app->jbstring->getId('jbdownload');
        $element = $parent->element;
        $layout  = $request->getCmd('layout');

        $element->loadConfigAssets();
        $params = array(
            'controller' => 'jbcart',
            'task'       => 'files',
            'format'     => 'raw',
            'layout'     => $layout
        );

        $html[] = '<div id="' . $id . '" class="creation-form jbdownload">';
        $html[] = '<input readonly="readonly" type="text" name="' . $this->_getName($controlName, $name) . '"
               value="' . $value . '" placeholder="' . JText::_('File') . '" />';
        $html[] = '</div>';

        $html[] = '<script type="text/javascript">';
        $html[] = 'jQuery(document).ready(function ($) {';
        $html[] = '$("#' . $id . ' input").Directories({
                            mode: "file",
                            url: "' . $this->app->jbrouter->admin($params) . '",
                            title: "' . JText::_('Files') . '",
                            msgDelete: "' . JText::_('Delete') . '"
                   });';
        $html[] = '});';
        $html[] = '</script>';

        return implode("\n", $html);
    }

    /**
     * @param $name
     * @param $value
     * @param $controlName
     * @param SimpleXMLElement $node
     *
     * @return string
     */
    public function cartFields($name, $value, $controlName, SimpleXMLElement $node)
    {
        $group    = $this->_getAttr($node, 'group');
        $position = $this->_getAttr($node, 'position', 'list');
        $multiple = (bool)$this->_getAttr($node, 'multiple', null);

        if (defined('JBCart::' . $group)) {
            $group = constant('JBCart::' . $group);
        }

        if (defined('JBCart::' . $position)) {
            $position = constant('JBCart::' . $position);
        }

        if (empty($group)) {
            return null;
        }

        $fields = $this->app->jbcartposition->loadPositions($group, array($position));
        if (empty($fields) || empty($fields[$position])) {
            return null;
        }
        $fields = $fields[$position];

        foreach ($fields as $element) {
            $options[] = $this->app->html->_('select.option', $element->identifier, $element->getName());
        }

        $style = 'size="5"';
        $name  = $this->_getName($controlName, $name);
        $name  = $multiple ? $name . '[]' : $name;

        $style .= $multiple ? ' multiple="multiple"' : null;

        $select =
            $this->app->html->_('select.genericlist', $options, $name, $style,
                'value', 'text', $value);

        return $select;
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param                  $parent
     *
     * @return string
     */
    public function notificationRecipients($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $jbhtml = $this->app->jbhtml;

        $value = $this->app->data->create($value);

        $html = array();
        $name = $this->_getName($controlName, $name);

        $adminName = $name . '[admin][]';
        $userName  = $name . '[user][]';
        $advName   = $name . '[advanced]';

        $adminAttrs = array(
            'multiple' => 'true',
            'style'    => 'height: 150px;'
        );

        $fields = $this->app->jbcartposition->loadPositions(JBCart::CONFIG_FIELDS, array(JBCart::DEFAULT_POSITION));
        $fields = $fields['list'];
        $forms  = array();

        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                $forms[$key] = $field->getName();
            }
        }

        $userOptions = array_merge(array(
            'profile' => JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_USER_EMAIL'),
        ), $forms);

        $userAttrs = array(
            'multiple' => true
        );

        $html[] =
            $this->spacer($name, JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_ADMIN_GROUPS'), $controlName, $node,
                $parent);
        $html[] =
            JHtml::_('access.usergroup', $adminName, $value->get('admin', array()), $jbhtml->buildAttrs($adminAttrs),
                false);

        $html[] = $this->spacer($userName, JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_USER'), $controlName, $node,
            $parent);
        $html[] =
            $this->app->html->_('select.genericlist', $userOptions, $userName, $jbhtml->buildAttrs($userAttrs), 'value',
                'text', $value->get('user', array()));

        $html[] =
            $this->spacer($userName, JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_ADVANCED_EMAIL'), $controlName,
                $node, $parent);
        $html[] = $jbhtml->text($advName, $value->get('advanced', ''));

        return implode("\n", $html);
    }

    /**
     * Render boolean list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function bool($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $optionList = array(
            0 => 'JBZOO_NO',
            1 => 'JBZOO_YES'
        );

        return $this->_renderRadio($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render boolean list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function boolGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('bool', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render boolean list
     * TODO Move queries to models
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function menuItems_j25($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $db = JFactory::getDbo();

        // Load the list of menu types
        $db->setQuery('SELECT menutype, title FROM #__menu_types ORDER BY title');
        $menuTypes = $db->loadObjectList();

        // load the list of menu items
        $db->setQuery('SELECT id, parent_id, title, menutype, type FROM #__menu WHERE published = "1" ORDER BY menutype, parent_id, ordering');
        $menuItems = $db->loadObjectList();

        // Establish the hierarchy of the menu
        $children = array();
        if ($menuItems) {
            // First pass - collect children
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        // Second pass - get an indent list of the items
        $list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        // Assemble into menutype groups
        $n = count($list);

        $groupedList = array();
        foreach ($list as $k => $v) {
            $groupedList[$v->menutype][] = & $list[$k];
        }

        // Assemble menu items to the array
        $options = array(
            '0' => JText::_('JOPTION_SELECT_MENU_ITEM')
        );

        foreach ($menuTypes as $type) {

            if (isset($groupedList[$type->menutype])) {

                $n = count($groupedList[$type->menutype]);
                for ($i = 0; $i < $n; $i++) {

                    $item = & $groupedList[$type->menutype][$i];

                    $options[$item->id] = $item->treename;
                }
            }

        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * TODO Move queries to models
     * Render boolean list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function menuItems_j3($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

        // Get the menu items.
        $items = MenusHelper::getMenuLinks();

        // Build the groups arrays.
        $options = array();
        foreach ($items as $menu) {
            foreach ($menu->links as $link) {
                $options[$link->value] = $link->text;
            }
        }


        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render textarea
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function textarea($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $enabled = $this->_getAttr($node, 'editor', 'false');

        $name   = $this->_getName($controlName, $name);
        $rows   = $this->_getAttr($node, 'rows', 10);
        $cols   = $this->_getAttr($node, 'cols', 10);
        $width  = $this->_getAttr($node, 'width', 50);
        $height = $this->_getAttr($node, 'height', 50);

        $attrs = array(
            'name'        => $name,
            'placeholder' => $this->_getAttr($node, 'placeholder', null),
            'class'       => $this->_getAttr($node, 'class', null),
            'rows'        => $rows,
            'cols'        => $cols,
        );

        $editor = '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';
        if ($enabled === 'true' && (int)$this->app->joomla->version->isCompatible('3.0')) {

            if ($this->app->jbrequest->isAjax()) {

                $lang    = JFactory::getLanguage();
                $version = new JVersion;

                $attributes = array(
                    'charset'      => 'utf-8',
                    'lineend'      => 'unix',
                    'tab'          => '  ',
                    'language'     => $lang->getTag(),
                    'direction'    => $lang->isRTL() ? 'rtl' : 'ltr',
                    'mediaversion' =>
                        (method_exists($version, 'getMediaVersion') === true ? $version->getMediaVersion() : null)
                );

                $document = JDocument::getInstance('html', $attributes);

                JFactory::$document = $document;
            }

            $editor = $this->app->editor->display($name, $value, $width, $height, $cols, $rows);
        }

        return $editor;
    }

    /**
     * Render colors fields
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function colors($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $html  = array();
        $id    = $this->app->jbstring->getId('jbcolor-input-');
        $divId = $this->app->jbstring->getId('jbcolor-');

        $this->app->jbassets->initJBColorElement($divId);

        $attrs = array(
            'name'  => $this->_getName($controlName, $name),
            'class' => 'jbcolor-textarea ' . $this->_getAttr($node, 'class'),
        );

        $colorAttrs = array(
            'placeholder' => JText::_('JBZOO_COLOR'),
            'class'       => 'jbcolor-input jbcolor  minicolors-position-bottom',
            'id'          => $id
        );

        $html[] = '<div id="' . $divId . '" class="jbzoo-picker">';

        $html[] = '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';

        $html[] = '<div class="jbpicker">';
        $html[] = '<input type="text" placeholder="' . JText::_('JBZOO_NAME') . '"  class="jbcolor-input jbname" />';
        $html[] = '<input type="text" ' . $this->app->jbhtml->buildAttrs($colorAttrs) . ' />';
        $html[] = '<span title="' . JText::_('JBZOO_JBCOLOR_ADD_COLOR') . '" class="jsColorAdd"></span>';
        $html[] = '</div></div>';

        $html[] = '<script type="text/javascript">';
        $html[] = 'jQuery(document).ready(function ($) {';
        $html[] = '$("#' . $divId . '").JBColorElement({
                        text: "' . JText::_('JBZOO_JBCOLOR_COLOR_EXISTS') . '"
                        });
                   });';
        $html[] = '</script>';

        return implode("\n", $html);
    }

    /**
     * Render element list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function elementList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $types = $this->_getAttr($node, 'types', '');

        if ($types) {
            $types = explode(',', $types);
        }

        $files = JFolder::files($this->app->path->path('jbtypes:'), '\.config');

        $optionList = array();
        foreach ($files as $file) {

            $json = $this->app->jbfile->read($this->app->path->path('jbtypes:' . $file));
            $data = @json_decode($json, true);

            if (!$data) {
                continue;
            }

            foreach ($data['elements'] as $key => $element) {
                if (in_array($element['type'], $types)) {
                    $optionList[$key] = $data['name'] . ' - ' . $element['name'];
                }
            }

        }

        if (!empty($optionList) && !(int)$this->_getAttr($node, 'multiple', '0')) {
            $optionList = $this->app->jbarray->unshiftAssoc($optionList, '', ' - ');
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render element list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function elementListByType($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $showCode = (int)$this->_getAttr($node, 'core', 0);
        $showUser = (int)$this->_getAttr($node, 'user', 1);
        $typeList = explode(',', (string)$this->_getAttr($node, 'types', ''));

        $type = (array)$this->app->jbrequest->get('cid');
        $type = current($type);

        $file = $this->app->path->path('jbtypes:' . $type . '.config');
        if ($file && $json = $this->app->jbfile->read($file)) {
            $data = json_decode($json, true);
        }

        $optionList = array('' => '- no select -');
        if (isset($data['elements']) && !empty($data['elements'])) {
            foreach ($data['elements'] as $key => $element) {

                if (!empty($typeList) && !in_array($element['type'], $typeList)) {
                    continue;
                }

                if ($showCode && preg_match('#^_#', $key)) {
                    $optionList[$key] = $element['name'];
                }

                if ($showUser && !preg_match('#^_#', $key)) {
                    $optionList[$key] = $element['name'];
                }
            }
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render element list
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function JBElementListByType($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $elTypes = $this->_getAttr($node, 'types', '');
        $layout  = $this->_getAttr($node, 'layout', 'teaser');

        if ($elTypes) {
            $elTypes = explode(',', $elTypes);
        }

        $optionList = array();
        $result     = array();

        // TODO remove paths hardcode
        $paths = array(
            $this->app->path->path('jbapp:templates/catalog/renderer/item/positions.config'),
            JPATH_BASE . '/modules/mod_zooitem/renderer/item/positions.config',
            JPATH_BASE . '/plugins/system/widgetkit_zoo/widgets/slideset/renderer/item/positions.config',
            $this->app->path->path('mod_jbzoo_item:renderer/item/positions.config')
        );
        $files = JFolder::files($this->app->path->path('jbtypes:'), '\.config');

        $types = $this->app->zoo->getApplication()->getTypes();
        $types = $this->app->data->create($types);


        foreach ($types as $type) {
            foreach ($paths as $path) {

                $config = $this->app->parameter->create($this->app->jbfile->read($path));
                $param  = $config->get(JBZOO_APP_GROUP . '.' . $type->id . '.' . $layout);
                if (empty($param)) {
                    continue;
                }

                $result[] = $param;
            }
        }

        foreach ($files as $file) {

            $json = $this->app->jbfile->read($this->app->path->path('jbtypes:' . $file));
            $data = @json_decode($json, true);

            if (!$data) {
                continue;
            }

            foreach ($data['elements'] as $key => $element) {

                if (in_array($element['type'], $elTypes)) {

                    if (!empty($result)) {
                        foreach ($result as $elem) {

                            $elem = $this->app->data->create($elem);
                            $kee  = $elem->searchRecursive($key);

                            $position = $this->app->data->create($elem->get($kee));
                            for ($i = 0; $i < count($position); $i++) {
                                if ($position[$i]['element'] == $key) {
                                    $optionList[json_encode($position[$i])] = $data['name'] . ' - ' . $element['name'];
                                }
                            }

                        }
                    }
                }
            }
        }

        $optionList = array_unique($optionList);

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Field for type - jbpricefields
     *
     * @param                  $name
     * @param                  $values
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param                  $parent
     *
     * @return string
     */
    public function priceAdvanceFields($name, $values, $controlName, SimpleXMLElement $node, $parent)
    {
        $html   = array();
        $unique = $this->app->jbstring->getId('jbprice-advance-fields-');

        $html[] = '<div class="jbprice-advance-fields" id="' . $unique . '">';

        $html[] = '<div class="hidden">';

        $html[] = '<div class="jbprice-fields-parameter clearfix">';
        $html[] = '<input type="text" name="' . $controlName . '['
                  . $name . '][params][0][name]" class="jsJBPriceParamAddValue" disabled>';
        $html[] =
            '<input type="hidden" name="' . $controlName . '[' . $name . '][params][0][value]" value="" disabled>';
        $html[] = '<a class="jsJBPriceDeleteParam jbprice-adv-delete-param" href=""></a>';
        $html[] = '<div class="jbprice-fields-options"></div>';
        $html[] = '<a href="javascript:void(0)" class="jbprice-addOption jsJBPriceAddOption clearfix"></a>';
        $html[] = '</div>';

        $html[] = '<div class="jbprice-field-option">';
        $html[] = '<input type="text" name="' . $controlName . '['
                  . $name . '][params][0][option][0][name]" class="jsJBPriceOptionAddValue" disabled/>';
        $html[] = '<input type="hidden" name="' . $controlName . '['
                  . $name . '][params][0][option][0][value]" value=""disabled/>';
        $html[] = '<a class="jsJBPriceDeleteOption jbprice-adv-delete-option" href=""></a>';
        $html[] = '</div></div>';

        if (!empty($values['params'])) {

            foreach ($values['params'] as $key => $value) {

                $html[] = '<div class="jbprice-fields-parameter clearfix">';
                $html[] =
                    '<input type="text" name="' . $controlName . '[' . $name . '][params][' . $key . '][name]" value="'
                    . $value['name'] . '" class="jsJBPriceParamAddValue" />';
                $html[] = '<input type="hidden" name="' . $controlName . '[' . $name . '][params][' . $key
                          . '][value]" value="' . $value['value'] . '" />';

                $html[] = '<a class="jsJBPriceDeleteParam jbprice-adv-delete-param" href=""></a>';
                $html[] = '<div class="jbprice-fields-options">';

                if (!empty($value['option'])) {

                    for ($i = 0; $i < count($value['option']); $i++) {

                        if (empty($value['option'][$i]['name']) || (empty($value['option'][$i]['value']))) {
                            continue;
                        }

                        $html[] = '<div class="jbprice-field-option">';
                        $html[] = '<input type="text" name="' . $controlName . '[' . $name . '][params][' . $key
                                  . '][option][' . $i . '][name]" value="'
                                  . $value['option'][$i]['name'] . '" class="jsJBPriceOptionAddValue" />';
                        $html[] = '<input type="hidden" name="' . $controlName . '[' . $name . '][params][' . $key
                                  . '][option][' . $i . '][value]" value="' . $value['option'][$i]['value'] . '" />';
                        $html[] = '<a class="jsJBPriceDeleteOption jbprice-adv-delete-option" href=""></a>';
                        $html[] = '</div>';

                    }

                }

                $html[] = '</div>';
                $html[] = '<a href="javascript:void(0)" class="jbprice-addOption jsJBPriceAddOption clearfix"></a>';
                $html[] = '</div>';
            }
        }
        $html[] = '<a href="javascript:void(0)" class="jbprice-addParam"> <span class="jsJBPriceAddParam">'
                  . JText::_('JBZOO_JBPRICE_ADVANCEFIELDS_PARAMETER_ADD')
                  . '</span></a>';

        $html[] = '</div>';

        $url = $this->app->link(array(
            'controller' => 'manager',
            'format'     => 'raw',
            'task'       => 'getalias',
            'force_safe' => 1
        ), false);

        $html[] = '<script type="text/javascript">';
        $html[] = 'jQuery(document).ready(function ($) {';
        $html[] = '$("#' . $unique . '").JBZooPriceAdvanceFields({"url": "' . $url . '"}); });';
        $html[] = '</script>';

        return implode($html);
    }

    /**
     * @param $name
     * @param $value
     * @param $controlName
     * @param SimpleXMLElement $node
     * @param $parent
     *
     * @return mixed
     */
    public function jbpriceTemplates($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $class = $this->_getAttr($node, 'renderer', 'jbprice');

        $renderer   = $this->app->jbrenderer->create($class);
        $optionList = $renderer->getLayouts($class);

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render element id
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function elementId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if (isset($parent->element->identifier)) {
            return $parent->element->identifier;
        }

        return '';
    }

    /**
     * Render element id
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function spacer($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($value) {
            return '<div class="field-jbspacer"><b> -= ' . JText::_($value) . ' =- </b></div>';
        }

        return null;
    }

    /**
     * Render custom description
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function desc($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($value) {
            return '<span style="font-size:1.1em">' . JText::_($value) . '</span>';
        }

        return null;
    }

    /**
     * Render related fields
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function relatedFields($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $stdFields = array('_itemname', '_itemtag', '_itemcategory', '_itemfrontpage');

        $typesPath = $this->app->path->path('jbtypes:');
        $files     = JFolder::files($typesPath, '.config');

        $coreGrp = JText::_('JBZOO_FIELDS_CORE');
        $options = array($coreGrp => array());
        foreach ($stdFields as $stdField) {
            $options[$coreGrp][] = $this->_createOption($stdField, 'JBZOO_FIELDS_CORE' . $stdField);
        }

        foreach ($files as $file) {
            $fileContent = $this->app->jbfile->read($typesPath . '/' . $file);
            $typeData    = json_decode($fileContent, true);

            $elements = array();
            foreach ($typeData['elements'] as $elementId => $element) {

                if (strpos($elementId, '_') === 0) {
                    continue;
                }

                $elements[] = $this->_createOption($elementId, $element['name']);
            }

            $options[$typeData['name']] = $elements;
        }

        $name  = $this->_getName($controlName, $name);
        $attrs = array();
        if ($this->_getAttr($node, 'multiple', '0') == '1') {
            $attrs['multiple'] = 'multiple';
            $attrs['size']     = $this->_getAttr($node, 'size', '10');
            $name .= '[]';
        }

        return JHtml::_('select.groupedlist', $options, $name, array(
            'list.attr'   => $this->app->jbhtml->buildAttrs($attrs),
            'list.select' => $value,
            'group.items' => null,
        ));
    }

    /**
     * Render hidden timestamp
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function password($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $attrs = array(
            'name'         => $this->_getName($controlName, $name),
            'type'         => 'password',
            'value'        => $value,
            'autocomplete' => 'off',
        );

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render key-value pair
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function keyValue($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $html = array();

        if (empty($value)) {
            $value = array(array('key' => '', 'value' => ''));
        }

        $i = 0;
        foreach ($value as $valueItem) {

            if ($i != 0 && (!isset($valueItem['key']) || empty($valueItem['key']))) {
                continue;
            }

            if ($i == 0 && empty($valueItem['key'])) {
                $valueItem['value'] = '';
            }

            $html[] = '<div class="jbkeyvalue-row">';
            $html[] = '<input ' . $this->app->jbhtml->buildAttrs(array(
                    'placeholder' => JText::_('JBZOO_JBKEYVALUE_KEY'),
                    'type'        => 'text',
                    'name'        => $this->_getName($controlName, $name) . '[' . $i . '][key]',
                    'value'       => isset($valueItem['key']) ? $valueItem['key'] : '',
                    'class'       => isset($class) ? $class : ''
                )) . ' />';

            $html[] = '<strong>&nbsp;=&nbsp;</strong>';

            $html[] = '<input ' . $this->app->jbhtml->buildAttrs(array(
                    'placeholder' => JText::_('JBZOO_JBKEYVALUE_VALUE'),
                    'type'        => 'text',
                    'name'        => $this->_getName($controlName, $name) . '[' . $i . '][value]',
                    'value'       => isset($valueItem['value']) ? $valueItem['value'] : '',
                    'class'       => isset($class) ? $class : ''
                )) . ' />';

            $html[] = '</div>';

            $i++;
        }

        $output = implode("\n ", $html);
        $output .= '<a href="#jbkeyvalue-add" class="jsKeyValueAdd">' . JText::_('JBZOO_JBKEYVALUE_ADD') . '</a>';

        return '<div class="jsKeyValue">' . $output . '</div>';
    }

    /**
     * Render itemOrder global
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function itemOrderGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('itemOrder', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render itemOrder
     *
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    public function itemOrder($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $customName = $this->_getName($controlName, $name);

        $value     = (empty($value) || !is_array($value)) ? array('_jbzoo_none') : $value;
        $allValues = array_chunk($value, 3);

        $html = array();
        foreach ($allValues as $index => $valueRow) {

            foreach ($valueRow as $key => $valueRowItem) {
                $valueRow[$key] = preg_replace('#_jbzoo_[0-9]_#i', '_jbzoo_' . $index . '_', $valueRowItem);
            }

            $html[] = $this->_renderItemOrderRow($valueRow, $customName, $index);
        }

        $html = array_filter($html);
        if (empty($html)) {
            $html[] = $this->_renderItemOrderRow(array('_jbzoo_empty'), $customName);
        }

        $output = '<div class="jsItemOrder jbzoo-itemorder">'
                  . '<div class="jbzoo-itemorder-row">' . implode("</div><div class=\"jbzoo-itemorder-row\">\n ", $html)
                  . '</div>'
                  . '<br>'
                  . '<a href="#jbitemorder-add" class="jsItemOrderAdd">' . JText::_('JBZOO_SORT_ADD') . '</a>'
                  . '</div>';

        return $output;
    }

    /**
     * Render itemorder row
     *
     * @param     $rowValue
     * @param     $customName
     * @param int $index
     *
     * @return null|string
     */
    protected function _renderItemOrderRow($rowValue, $customName, $index = 0)
    {
        $values  = $this->app->data->create($this->app->jbarray->addToEachKey($rowValue, 'key_'));
        $options = $this->getSortElementsOptionList($index);

        $orderValue = $values->get('key_0');
        if (empty($orderValue) || preg_match('#_jbzoo(.*)none#i', $orderValue)) {
            return null;
        }

        $ctrl = array();
        $i    = 0;

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_FIELD')
                  . ': </span>' . JHtml::_('select.groupedlist', $options, $customName . '[]', array(
                'list.attr'   => $this->app->jbhtml->buildAttrs(array()),
                'list.select' => $values->get('key_' . $i++),
                'group.items' => null,
            ));

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_AS')
                  . ': </span>' . $this->app->jbhtml->select(array(
                '_jbzoo_' . $index . '_mode_s' => JText::_('JBZOO_SORT_AS_STRINGS'),
                '_jbzoo_' . $index . '_mode_n' => JText::_('JBZOO_SORT_AS_NUMBERS'),
                '_jbzoo_' . $index . '_mode_d' => JText::_('JBZOO_SORT_AS_DATES'),
            ), $customName . '[]', '', $values->get('key_' . $i++));

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_ORDER')
                  . ': </span>' . $this->app->jbhtml->select(array(
                '_jbzoo_' . $index . '_order_asc'    => JText::_('JBZOO_SORT_ORDER_ASC'),
                '_jbzoo_' . $index . '_order_desc'   => JText::_('JBZOO_SORT_ORDER_DESC'),
                '_jbzoo_' . $index . '_order_random' => JText::_('JBZOO_SORT_ORDER_RANDOM'),
            ), $customName . '[]', '', $values->get('key_' . $i++));

        return '<div class="jbzoo-itemorder-row-field">'
               . implode("</div><div class=\"jbzoo-itemorder-row-field\">\n ", $ctrl)
               . '</div>';
    }

    /**
     * Get pre-prepared options list for itemorder list
     *
     * @param int $index
     * @param string $prefix
     *
     * @return array
     */
    public function getSortElementsOptionList($index = 0, $prefix = '_jbzoo_<INDEX>')
    {
        $stdFields = array(
            'corename',
            'corealias',
            'corecreated',
            'corehits',
            'coremodified',
            'corepublish_down',
            'corepublish_up',
            'coreauthor',
        );

        if ($prefix) {
            $prefix = str_replace('<INDEX>', $index, $prefix) . '_field_';
        }

        $excludeType = JBModelSearchindex::model()->getExcludeTypes();

        $typesPath = $this->app->path->path('jbtypes:');
        $files     = JFolder::files($typesPath, '.config');

        // add std fields
        $coreGrp = JText::_('JBZOO_FIELDS_CORE');
        $options = array($coreGrp => array(
            $prefix . '_none' => JText::_('JBZOO_FIELDS_CORE_NONE'),
            'random'          => JText::_('JBZOO_SORT_ORDER_RANDOM')
        ));
        foreach ($stdFields as $stdField) {
            $options[$coreGrp][] = $this->_createOption($prefix . $stdField, 'JBZOO_FIELDS_CORE_' . $stdField);
        }

        // add custom fields
        foreach ($files as $file) {
            $fileContent = $this->app->jbfile->read($typesPath . '/' . $file);
            $typeData    = json_decode($fileContent, true);

            $elements = array();
            foreach ($typeData['elements'] as $elementId => $element) {

                if (strpos($elementId, '_') === 0 || in_array($element['type'], $excludeType, true)) {
                    continue;
                }

                if ($element['type'] == 'jbpriceadvance') {
                    $elements = array_merge($elements, $this->_getSortJBPriceOptionList($element, $elementId, $prefix));
                } else {
                    $elements[] = $this->_createOption($prefix . $elementId, $element['name'], false);
                }

            }

            $options[$typeData['name']] = $elements;
        }

        return $options;
    }

    /**
     * @param array $element
     * @param string $elementId
     * @param string $prefix
     *
     * @return array
     */
    protected function _getSortJBPriceOptionList($element, $elementId, $prefix)
    {
        $list = array();

        $keyPrefix = $prefix . $elementId;

        $list[] = $this->_createOption($keyPrefix . '__sku', $element['name'] . ' - Sku', false);
        $list[] = $this->_createOption($keyPrefix . '__price', $element['name'] . ' - Basic', false);
        $list[] = $this->_createOption($keyPrefix . '__total', $element['name'] . ' - Total', false);

        return $list;
    }

    /**
     * Check is current
     *
     * @param SimpleXMLElement $node
     * @param AppParameterForm $parent
     *
     * @return bool
     */
    public function isGlobal(SimpleXMLElement $node, $parent)
    {
        $request = $this->app->jbrequest;

        if ($request->is('option', 'com_zoo')) {

            if ($request->isCtrl('configuration')) {
                return false;
            }

            if (($request->isCtrl('category') && ($request->is('task', 'edit') || $request->is('task', 'add')))
                || ($request->isCtrl('frontpage'))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Render radio params
     *
     * @param array $optionsList
     * @param string $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     *
     * @return mixed
     */
    protected function _renderRadio($optionsList, $value, $controlName, SimpleXMLElement $node)
    {
        $html = array();
        foreach ($optionsList as $key => $option) {
            $id         = 'radio-' . $this->app->jbstring->getId();
            $attributes = array(
                'id'    => $id,
                'type'  => 'radio',
                'name'  => $controlName,
                'value' => $key
            );

            if ($key == $value) {
                $attributes = array_merge($attributes, array('checked' => 'checked'));
            }

            $html[] = '<input ' . $this->app->jbhtml->buildAttrs($attributes) . ' /> '
                      . '<label ' . $this->app->jbhtml->buildAttrs(array('for' => $id)) . '>'
                      . JText::_($option)
                      . '</label>';
        }

        return implode(" \n", $html);
    }

    /**
     * Render layout list
     *
     * @param string $method
     * @param string $name
     * @param string|array $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     *
     * @return mixed
     */
    protected function _global($method, $name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $this->app->document->addScript('fields:global.js');

        $id     = 'listglobal-' . $this->app->jbstring->getId();
        $global = $parent->getValue((string)$name) === null;

        $html   = array();
        $html[] = '<div class="global list">';
        $html[] = '<input id="' . $id . '" type="checkbox"' . ($global ? ' checked="checked"' : '') . ' />';
        $html[] = '<label for="' . $id . '">' . JText::_('Global') . '</label>';
        $html[] = '<div class="input">';
        $html[] = call_user_func_array(
            array($this, $method),
            array($name, $value, $controlName, $node, $parent)
        );
        $html[] = '</div></div>';

        return implode("\n ", $html);
    }

    /**
     * Render list params
     *
     * @param array $optionsList
     * @param string $value
     * @param string $controlName
     * @param SimpleXMLElement $node
     *
     * @return mixed
     */
    protected function _renderList($optionsList, $value, $controlName, SimpleXMLElement $node)
    {
        $attributes = array();
        if ($this->_getAttr($node, 'multiple', '0') == '1') {
            $attributes['multiple'] = 'multiple';
            $attributes['size']     = $this->_getAttr($node, 'size', '10');
            $controlName .= '[]';
        }

        $attributes['class'] = $this->_getAttr($node, 'class', 'inputbox');

        $options = $this->app->html->listOptions($optionsList);

        return $this->app->html->genericList($options, $controlName, $attributes, 'value', 'text', $value);
    }

    /**
     * @param SimpleXMLElement $node
     * @param string $attrName
     * @param mixed $default
     *
     * @return bool|string
     */
    protected function _getAttr(SimpleXMLElement $node, $attrName, $default = null)
    {
        $result = $node->attributes()->{$attrName};

        if ($result) {
            return (string)$result;
        }

        return $default;
    }

    /**
     * Get name
     *
     * @param $controlName
     * @param $name
     *
     * @return string
     */
    protected function _getName($controlName, $name)
    {
        return $controlName . '[' . $name . ']';
    }

    /**
     * Create option instance
     *
     * @param string $key
     * @param string $value
     * @param bool $translate
     *
     * @return mixed
     */
    protected function _createOption($key, $value, $translate = true)
    {
        $name = $translate ? JText::_($value) : $value;

        return JHtml::_('select.option', $key, $name, 'value', 'text');
    }

}
