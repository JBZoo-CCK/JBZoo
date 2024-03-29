<?php
use Joomla\String\StringHelper;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filesystem\Folder;

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
 * Class JBFieldHelper
 */
class JBFieldHelper extends AppHelper
{
    /**
     * Render currency list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementCode($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if (empty($value) && $parent->element) {
            $value = $parent->element->identifier;
        }

        $attrs = [
            'name'         => $this->_getName($controlName, $name),
            'type'         => 'text',
            'value'        => $value,
            'autocomplete' => 'off',
        ];

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render currency list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function currencyList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $currencyList = $this->app->jbmoney->getCurrencyList();

        unset($currencyList['%']);

        if ((int)$this->_getAttr($node, 'showall', 0)) {
            $currencyList = $this->app->jbarray->unshiftAssoc($currencyList, 'all', JText::_('JBZOO_ALL'));
        }

        return $this->_renderList($currencyList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render application list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function applicationList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $appList = $this->app->table->application->all();
        $basketOnly = (int)$this->_getAttr($node, 'cart_only', 0);

        $options = [];
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
     * Render application list
     * @param string           $name
     * @param string|array     $value
     * @param                  $control_name
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function jbApplication($name, $value, $control_name, SimpleXMLElement $node, $parent)
    {
        // init vars
        $table = $this->app->table->application;

        // set modes
        $modes = [];
        $common_name = $this->_getName($control_name, $name);
        if ((int)$this->_getAttr($node, 'allitems', 0)) {
            $modes[] = $this->app->html->_('select.option', 'all', JText::_('All Items'));
        }

        if ((int)$this->_getAttr($node, 'categories', 0)) {
            $modes[] = $this->app->html->_('select.option', 'categories', JText::_('Categories'));
        }

        if ((int)$this->_getAttr($node, 'types', 0)) {
            $modes[] = $this->app->html->_('select.option', 'types', JText::_('Types'));
        }

        if ((int)$this->_getAttr($node, 'items', 0)) {
            $modes[] = $this->app->html->_('select.option', 'item', JText::_('Item'));
        }

        // create application/category select
        $cats = [];
        $types = [];
        $options = [$this->app->html->_('select.option', '', '- ' . JText::_('Select Application') . ' -')];

        $unique_id = $this->app->jbstring->getId('jbapplication-');
        foreach ($table->all(['order' => 'name']) as $application) {

            $app_value = isset($value['app']) ? $value['app'] : null;
            // application option
            $options[] = $this->app->html->_('select.option', $application->id, $application->name);

            // create category select
            if ((int)$this->_getAttr($node, 'categories', 0)) {

                $cat_value = isset($value['category']) && StringHelper::strlen($value['category']) > 0 ? $value['category'] : null;
                $attribs = 'class="category app-' . $application->id . ($app_value != $application->id ? ' hidden' : null) . '" data-category="' . $common_name . '[category]"';
                $opts = [];
                if ((int)$this->_getAttr($node, 'frontpage', 0)) {
                    $opts[] = $this->app->html->_('select.option', '', '&#8226;' . JText::_('Frontpage'));
                }
                $cats[] = $this->app->html->_('zoo.categorylist', $application, $opts,
                    ($app_value == $application->id ? $common_name . '[category]' : null), $attribs, 'value', 'text',
                    $cat_value);
            }

            // create types select
            if ((int)$this->_getAttr($node, 'types', 0)) {
                $opts = [];

                foreach ($application->getTypes() as $type) {
                    $opts[] = $this->app->html->_('select.option', $type->id, $type->name);
                }

                $type_value = isset($value['type']) ? $value['type'] : null;
                $attribs = 'class="type app-' . $application->id . ($app_value != $application->id ? ' hidden' : null) . '" data-type="' . $common_name . '[type]"';
                $types[] = $this->app->html->_('select.genericlist', $opts, $common_name . '[type]', $attribs, 'value',
                    'text', $type_value, 'application-type-' . $application->id);
            }
        }

        // create html
        $html[] = '<div id="' . $unique_id . '" class="zoo-application">';
        $html[] = $this->app->html->_('select.genericlist', $options, $common_name . '[app]', 'class="application"',
            'value', 'text', $app_value);

        $mode_value = isset($value['mode']) ? $value['mode'] : null;
        // create mode select
        if (count($modes) > 1) {
            $html[] = $this->app->html->_('select.genericlist', $modes, $common_name . '[mode]', 'class="mode"',
                'value', 'text', $mode_value);
        }

        // create categories html
        if (!empty($cats)) {
            $html[] = '<div class="categories">' . implode(PHP_EOL, $cats) . '</div>';
        }

        // create types html
        if (!empty($types)) {
            $html[] = '<div class="types">' . implode(PHP_EOL, $types) . '</div>';
        }

        // create items html
        $link = '';
        if ((int)$this->_getAttr($node, 'items', 0)) {
            $field_name = $common_name . '[item_id]';
            $item_name = JText::_('Select Item');
            $item_id = isset($value['item_id']) ? $value['item_id'] : null;

            if ($item_id > 0) {
                $item = $this->app->table->item->get($item_id);
                $item_name = $item ? $item->name : $item_name;
            }

            $link = $this->app->link([
                'controller' => 'item',
                'task'       => 'element',
                'tmpl'       => 'component',
                'func'       => 'selectZooItem',
                'object'     => $unique_id,
            ], false);

            $html[] = '<div class="item">';
            $html[] = '<input type="text" id="' . $unique_id . '_name" value="' . htmlspecialchars($item_name,
                    ENT_QUOTES, 'UTF-8') . '" disabled="disabled" />';
            $html[] = '<a class="modal" title="' . JText::_('Select Item') . '"  href="#" rel="{handler: \'iframe\', size: {x: 850, y: 500}}">' . JText::_('Select') . '</a>';
            $html[] = '<input type="hidden" id="' . $unique_id . '_id" name="' . $field_name . '" value="' . (int)$item_id . '" />';
            $html[] = '</div>';

        }

        $html[] = '</div>';

        $this->app->document->addScript('fields:zooapplication.js');
        $javascript = $this->app->jbassets->widget('#' . $unique_id, 'ZooApplication', [
            'url'           => $link,
            'msgSelectItem' => JText::_('Select Item'),
        ], true);

        echo implode(PHP_EOL, $html) . $javascript;
    }

    /**
     * Render application list
     * @param string           $name
     * @param string|array     $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function userData($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php'); 
        $context = 'com_users.user';
        $fieldsarr = array();
        $poleuser = JFactory::getUser()->id;
        $poleuserstd = new \stdClass;
        $poleuserstd->id = $poleuser;
        $bigProfile = FieldsHelper::getFields($context, $poleuserstd, false);

        foreach ($bigProfile as $poleProfile) {
            if (!empty($poleProfile->name)) {
                $fieldsarr[] = trim($poleProfile->name);
            }
        }
        
        $poleList = implode(',',$fieldsarr);
       
        $whiteList = ['name', 'username', 'email', 'registerDate', 'lastvisitDate'];
        $resultList = array_merge($whiteList,$fieldsarr);

        return $this->_renderList(['' => '--'] + $resultList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render layout list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function layoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $layoutName = str_replace('layout_', '', $this->_getAttr($node, 'name', ''));

        $auto = $this->_getAttr($node, 'auto', 'true');
        $path = JPath::clean(
            $this->app->path->path('jbtmpl:') . '/' .
            $this->app->jbenv->getTemplateName()
            . '/renderer'
            . '/' . $layoutName
        );

        $system = $this->app->path->path("jbapp:templates-system/renderer/{$layoutName}");

        if ($auto == 'true') {
            $options = ['__auto__' => JText::_('JBZOO_LAYOUT_AUTOSELECT')];
        }

        if (is_dir($path)) {
            $files = Folder::files($path, '^([-_A-Za-z0-9\.]*)\.php$', false, false, ['.svn', 'CVS']);
            foreach ($files as $tmpl) {
                $tmpl = basename($tmpl, '.php');
                $options[$tmpl] = $tmpl;
            }
        }



        if (is_dir($system) && $system) {
            $files = Folder::files($system, '^([-_A-Za-z0-9\.]*)\.php$', false, false, ['.svn', 'CVS']);
            foreach ($files as $tmpl) {
                $tmpl = basename($tmpl, '.php');
                $options[$tmpl] = $tmpl;
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render layout list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function orderMacros($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $isTextarea = $this->_getAttr($node, 'textarea', 'true') == 'true';
        $inputName = $this->_getName($controlName, $name);

        $html = [];
        if ($isTextarea) {
            $attrs = [
                'cols'  => $this->_getAttr($node, 'cols', '25'),
                'rows'  => $this->_getAttr($node, 'rows', '3'),
                'name'  => $inputName,
                'class' => 'jbmacroslist-textarea',
            ];

            $html[] = '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';
        } else {
            $attrs = [
                'value'     => $value,
                'name'      => $inputName,
                'maxlength' => 255,
                'type'      => 'text',
                'class'     => 'jbmacroslist-text',
            ];

            $html[] = '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
        }

        $html[] = '<input type="button" class="uk-button uk-button-mini jbmacroslist-button jsShow" '
            . 'value="?" title="' . JText::_('JBZOO_ORDER_MACROS_LIST') . '" />';

        $list = $this->app->jbordermacros->getList();
        $html[] = JBZOO_CLR . '<ul class="jsMacrosList macros-list clear">';
        foreach ($list as $key => $macros) {
            $html[] = '<li><span class="jbmacroslist-row">' . $key . '</span> ' . $macros . '</li>';
        }
        $html[] = '</ul>';

        $html[] = $this->app->jbassets->widget('.jsOrderMacros', 'JBZooOrderMacrosList', [], true);

        return '<div class="jsOrderMacros jbmacroslist">' . implode(PHP_EOL, $html) . '</div>';
    }

    /**
     * Render layout list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementLayouts($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $path = $this->_getAttr($node, 'path', '');
        $lPath = $this->app->path->path($path);

        if (!$lPath) {
            return '<i>Undefined path: ' . $path . '</i>';
        }

        $options = [];
        if ($this->_getAttr($node, 'auto', 'true') == 'true') {
            $options = ['__auto__' => JText::_('JBZOO_LAYOUT_AUTOSELECT')];
        }

        $files = Folder::files($lPath, '^([-_A-Za-z0-9\.]*)\.php$', false, false, ['.svn', 'CVS']);
        foreach ($files as $tmpl) {
            $tmpl = basename($tmpl, '.php');
            $options[$tmpl] = $tmpl;
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node, false);
    }

    /**
     * Render submission layout list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function formLayoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = [
            '' => JText::_('JBZOO_CART_SELECT_ORDER_FORM'),
        ];

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
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function emailLayoutList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = [
            '' => JText::_('JBZOO_CART_SELECT_ORDER_FORM'),
        ];

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
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function submissionList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();

        $options = ['' => JText::_('JBZOO_CART_SELECT_ORDER_FORM')];
        if ($application) {
            foreach ($application->getSubmissions() as $submission) {
                $options[$submission->id] = $submission->name;
            }
        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render layout list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function layoutListGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('layoutList', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render typelist list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function types($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $options = [];

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
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function timestamp($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $attrs = [
            'name'  => $this->_getName($controlName, $name),
            'type'  => 'hidden',
            'value' => time(),
        ];

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render hidden timestamp
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function currentCategoryId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($cid = (array)$this->app->jbrequest->get('cid')) {
            return current($cid);
        }

        return '<em>' . JText::_('JBZOO_UNDEFINED') . '</em>';
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param                  $parent
     * @return string
     */
    public function currentApplicationId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $application = $this->app->zoo->getApplication();
        if (isset($application->id)) {
            return $application->id;
        }

        return '<em>' . JText::_('JBZOO_UNDEFINED') . '</em>';
    }

    /**
     * Render hidden timestamp
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function select($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $optionList = [];
        foreach ($node->children() as $option) {
            $optionList[$this->_getAttr($option, 'value', '')] = JText::_((string)$option);
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render hidden input with value
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function hiddenValue($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $attrs = [
            'name'  => $this->_getName($controlName, $name),
            'type'  => 'hidden',
            'value' => $value,
        ];

        return '<span class="badge badge-info">'.$value.'</span><input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return string $field
     */
    public function finder($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $request = $this->app->request;
        $html = [];
        $id = $this->app->jbstring->getId('jbdownload');
        $element = $parent->element;
        $layout = $request->getCmd('layout');

        $element->loadConfigAssets();
        $params = [
            'controller' => 'jbcart',
            'task'       => 'files',
            'format'     => 'raw',
            'layout'     => $layout,
        ];

        $html[] = '<div id="' . $id . '" class="creation-form jbdownload">';
        $html[] = '<input readonly="readonly" type="text" name="' . $this->_getName($controlName, $name) . '"
               value="' . $value . '" placeholder="' . JText::_('File') . '" />';
        $html[] = '</div>';

        $html[] = $this->app->jbassets->widget('#' . $id . ' input', 'Directories', [
            'mode'      => 'file',
            'url'       => $this->app->jbrouter->admin($params),
            'title'     => JText::_('Files'),
            'msgDelete' => JText::_('Delete'),
        ], true);

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function cartFields($name, $value, $controlName, SimpleXMLElement $node)
    {
        $group = $this->_getAttr($node, 'group');
        $position = $this->_getAttr($node, 'position', 'list');
        $multiple = (bool)$this->_getAttr($node, 'multiple', 0);
        $emptytext = '<i>' . JText::_($this->_getAttr($node, 'emptytext',
                'JBZOO_ELEMENT_CARTFIELDS_EMPTYTEXT')) . '</i>';

        if (defined('JBCart::' . $group)) {
            $group = constant('JBCart::' . $group);
        }

        if (defined('JBCart::' . $position)) {
            $position = constant('JBCart::' . $position);
        }

        if (empty($group)) {
            return $emptytext;
        }

        $fields = $this->app->jbcartposition->loadPositions($group, [$position]);

        if (empty($fields) || empty($fields[$position])) {
            return $emptytext;
        }

        $fields = $fields[$position];
        $options = [];
        foreach ($fields as $element) {
            $options[] = $this->app->html->_('select.option', $element->identifier, $element->getName());
        }

        $name = $this->_getName($controlName, $name);
        $name = $multiple ? $name . '[]' : $name;
        $attrs = $multiple ? ' multiple="multiple" size="5"' : null;

        if (!empty($options)) {
            return $this->app->html->_('select.genericlist', $options, $name, $attrs, 'value', 'text', $value);
        }

        return $emptytext;
    }

    /**
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function notificationRecipients($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $jbhtml = $this->app->jbhtml;

        $value = $this->app->data->create($value);
        $name = $this->_getName($controlName, $name);

        $html = [];

        // build select for user groups
        $html[] = $this->spacer('', JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_GROUPS'), $controlName, $node,
            $parent);
        $html[] = JHtml::_('access.usergroup', $name . '[groups][]', $value->get('groups', []), $jbhtml->buildAttrs([
            'multiple' => 'multiple',
            'size'     => '5'
        ]), false);


        // build select for custom fields
        $userOptions = [
            'usermail' => JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_USERMAIL'),
            'sitemail' => JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_SITEMAIL'),
        ];
        $elements = $this->app->jbcartposition->loadPositions(JBCart::CONFIG_FIELDS, [JBCart::DEFAULT_POSITION]);
        $elements = $elements['list'];
        if (!empty($elements)) {
            foreach ($elements as $key => $element) {
                $userOptions[$element->identifier] = $element->getName();
            }
        };
        $html[] = $this->spacer('', JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_ORDERFORM'), $controlName, $node,
            $parent);
        $html[] = $this->app->html->_('select.genericlist', $userOptions, $name . '[orderform][]',
            $jbhtml->buildAttrs(['multiple' => 'multiple', 'size' => '5']), 'value', 'text',
            $value->get('orderform', []));


        // text field
        $html[] = $this->spacer('', JText::_('JBZOO_NOTIFICATION_SENDEMAIL_RECIPIENT_CUSTOM'), $controlName, $node,
            $parent);
        $html[] = $jbhtml->text($name . '[custom]', $value->get('custom', ''));

        return implode(PHP_EOL, $html);
    }

    /**
     * Render boolean list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function bool($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $optionList = [
            0 => 'JBZOO_NO',
            1 => 'JBZOO_YES',
        ];

        return $this->_renderRadio($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render boolean list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function boolGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('bool', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render boolean list
     * TODO Move queries to models
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
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
        $children = [];
        if ($menuItems) {
            // First pass - collect children
            foreach ($menuItems as $v) {
                $pt = $v->parent_id;
                $list = @$children[$pt] ? $children[$pt] : [];
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        // Second pass - get an indent list of the items
        $list = JHtml::_('menu.treerecurse', 0, '', [], $children, 9999, 0, 0);

        // Assemble into menutype groups
        $n = count($list);

        $groupedList = [];
        foreach ($list as $k => $v) {
            $groupedList[$v->menutype][] = &$list[$k];
        }

        // Assemble menu items to the array
        $options = [
            '0' => JText::_('JOPTION_SELECT_MENU_ITEM'),
        ];

        foreach ($menuTypes as $type) {

            if (isset($groupedList[$type->menutype])) {

                $n = count($groupedList[$type->menutype]);
                for ($i = 0; $i < $n; $i++) {

                    $item = &$groupedList[$type->menutype][$i];

                    $options[$item->id] = $item->treename;
                }
            }

        }

        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * TODO Move queries to models
     * Render boolean list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function menuItems_j3($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

        // Get the menu items.
        $items = MenusHelper::getMenuLinks();

        // Build the groups arrays.
        $options = [];
        foreach ($items as $menu) {
            foreach ($menu->links as $link) {
                $options[$link->value] = $link->text;
            }
        }


        return $this->_renderList($options, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render textarea
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function textarea($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $enabled = $this->_getAttr($node, 'editor', 'false');

        $name = $this->_getName($controlName, $name);
        $rows = $this->_getAttr($node, 'rows', 10);
        $cols = $this->_getAttr($node, 'cols', 10);
        $width = $this->_getAttr($node, 'width', 50);
        $height = $this->_getAttr($node, 'height', 50);

        $attrs = [
            'name'        => $name,
            'placeholder' => $this->_getAttr($node, 'placeholder', null),
            'class'       => $this->_getAttr($node, 'class', null),
            'rows'        => $rows,
            'cols'        => $cols,
        ];

        $editor = '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';
        if ($enabled === 'true' && (int)$this->app->joomla->version->isCompatible('3.0')) {

            if ($this->app->jbrequest->isAjax()) {

                $lang = JFactory::getLanguage();
                $version = new JVersion;

                $attributes = [
                    'charset'      => 'utf-8',
                    'lineend'      => 'unix',
                    'tab'          => '  ',
                    'language'     => $lang->getTag(),
                    'direction'    => $lang->isRTL() ? 'rtl' : 'ltr',
                    'mediaversion' =>
                        (method_exists($version, 'getMediaVersion') === true ? $version->getMediaVersion() : null),
                ];

                $document = JDocument::getInstance('html', $attributes);

                JFactory::$document = $document;
            }

            $editor = $this->app->editor->display($name, $value, $width, $height, $cols, $rows);
        }

        return $editor;
    }

    /**
     * Render colors fields
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function colors($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $html = [];
        $id = $this->app->jbstring->getId('jbcolor-input-');
        $divId = $this->app->jbstring->getId('jbcolor-');

        $this->app->jbassets->initJBColorElement($divId);

        $attrs = [
            'name'  => $this->_getName($controlName, $name),
            'class' => 'jbcolor-textarea ' . $this->_getAttr($node, 'class'),
        ];

        $colorAttrs = [
            'placeholder' => JText::_('JBZOO_COLOR'),
            'class'       => 'jbcolor-input jbcolor  minicolors-position-bottom',
            'id'          => $id,
        ];

        $html[] = '<div id="' . $divId . '" class="jbzoo-picker">';

        $html[] = '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';

        $html[] = '<div class="jbpicker">';
        $html[] = '<input type="text" placeholder="' . JText::_('JBZOO_NAME') . '"  class="jbcolor-input jbname" />';
        $html[] = '<input type="text" ' . $this->app->jbhtml->buildAttrs($colorAttrs) . ' />';
        $html[] = '<span title="' . JText::_('JBZOO_JBCOLOR_ADD_COLOR') . '" class="jsColorAdd"></span>';
        $html[] = '</div></div>';

        $html[] = $this->app->jbassets->widget('#' . $divId, 'JBColorElement', [
            'text' => JText::_('JBZOO_JBCOLOR_COLOR_EXISTS'),
        ], true);

        return implode(PHP_EOL, $html);
    }

    /**
     * Render element list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementList($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $types = $this->_getAttr($node, 'types', '');
        $empty = $this->_getAttr($node, 'empty', 'JBZOO_ELEMENTLIST_NOT_FOUND');

        if ($types) {
            $types = explode(',', $types);
        }
        $files = Folder::files($this->app->path->path('jbtypes:'), '\.config');

        $optionList = [];
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

        if (empty($optionList)) {
            return JText::_($empty);
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * Render element list
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementListByType($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $showCode = (int)$this->_getAttr($node, 'core', 0);
        $showUser = (int)$this->_getAttr($node, 'user', 1);
        $typeList = explode(',', (string)$this->_getAttr($node, 'types', ''));
        $typeList = array_filter($typeList);

        $type = (array)$this->app->jbrequest->get('cid');
        $type = current($type);

        $file = $this->app->path->path('jbtypes:' . $type . '.config');
        if ($file && $json = $this->app->jbfile->read($file)) {
            $data = json_decode($json, true);
        }

        $optionList = ['' => '- no select -'];
        if (isset($data['elements']) && !empty($data['elements'])) {
            foreach ($data['elements'] as $key => $element) {

                if (!empty($typeList) && !in_array($element['type'], $typeList)) {
                    continue;
                }

                if ($showCode && preg_match('#^_#', $key)) {
                    $optionList[$key] = $element['name'] ? $element['name'] : $element['type'];
                }

                if ($showUser && !preg_match('#^_#', $key)) {
                    $optionList[$key] = $element['name'] ? $element['name'] : $element['type'];
                }
            }
        }

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node);
    }

    /**
     * @param                  $name
     * @param                  $value
     * @param                  $controlName
     * @param SimpleXMLElement $node
     * @param                  $parent
     * @return mixed
     */
    public function jbpriceTemplates($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $class = $this->_getAttr($node, 'renderer', 'jbprice');

        $renderer = $this->app->jbrenderer->create($class);
        $optionList = $renderer->getLayouts($class);

        return $this->_renderList($optionList, $value, $this->_getName($controlName, $name), $node, false);
    }

    /**
     * Render element id
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementId($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if (isset($parent->element->identifier)) {
            return $parent->element->identifier;
        }
    }

    /**
     * Render element id
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function elementDesc($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($desc = $parent->element->getMetaData('description')) {
            return '<span class="field-jbelement-desc">' . $desc . '</span>';
        }
    }

    /**
     * Render element id
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function currentRate($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        /** @type JBCartElementCurrency $element */
        $element = @$parent->element;
        if ($element) {

            $code = $element->getCode();
            try {
                $value = $element->getValue($code);
            } catch (JBCartElementCurrencyException $e) {
                $value = $element->getFallbackValue();
            }

            if ($value == 0 || $code == '%') {
                return '<em>' . JText::_('JBZOO_UNDEFINED') . '</em>';
            }

            $value = JBCart::val($value . ' ' . $code);

            return $value->html() . ' (' . $value->noStyle() . ')';
        }

        return JText::_('JBZOO_UNDEFINED');
    }

    /**
     * Render spacer text
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function spacer($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($value) {
            return '<div class="field-jbspacer"><b> -= ' . JText::_($value) . ' =- </b></div>';
        }
    }

    /**
     * Render important notice
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function important($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        if ($value) {
            return '<div class="field-jbimportant"><b>' . JText::_($value) . '</b></div>';
        }
    }

    /**
     * Render custom description
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
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
     * Render simple text field
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function text($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        // set attributes
        $attrs = [
            'type'  => 'text',
            'value' => JText::_($value),
            'name'  => $this->_getName($controlName, $name),
            'class' => isset($class) ? $class : '',
        ];

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . '/>';
    }

    /**
     * Render related fields
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function relatedFields($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        /* $stdFields = array('_itemname', '_itemtag', '_itemcategory', '_itemfrontpage');*/
        $stdFields = ['_itemname', '_itemtag', '_itemcategory', '_itemfrontpage', '_itemauthor'];


        $typesPath = $this->app->path->path('jbtypes:');
        $files = Folder::files($typesPath, '.config');

        $coreGrp = JText::_('JBZOO_FIELDS_CORE');
        $options = [$coreGrp => []];
        foreach ($stdFields as $stdField) {
            $options[$coreGrp][] = $this->_createOption($stdField, 'JBZOO_FIELDS_CORE' . $stdField);
        }

        foreach ($files as $file) {
            $fileContent = $this->app->jbfile->read($typesPath . '/' . $file);
            $typeData = json_decode($fileContent, true);

            $elements = [];
            foreach ($typeData['elements'] as $elementId => $element) {

                if (strpos($elementId, '_') === 0) {
                    continue;
                }

                $elements[] = $this->_createOption($elementId, $element['name']);
            }

            $options[$typeData['name']] = $elements;
        }

        $name = $this->_getName($controlName, $name);
        $attrs = [];
        if ($this->_getAttr($node, 'multiple', '0') == '1') {
            $attrs['multiple'] = 'multiple';
            $attrs['size'] = $this->_getAttr($node, 'size', '10');
            $name .= '[]';
        }

        return JHtml::_('select.groupedlist', $options, $name, [
            'list.attr'   => $this->app->jbhtml->buildAttrs($attrs),
            'list.select' => $value,
            'group.items' => null,
        ]);
    }

    /**
     * Render hidden timestamp
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function password($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $attrs = [
            'name'         => $this->_getName($controlName, $name),
            'type'         => 'password',
            'value'        => $value,
            'autocomplete' => 'off',
        ];

        return '<input ' . $this->app->jbhtml->buildAttrs($attrs) . ' />';
    }

    /**
     * Render key-value pair
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function keyValue($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $html = [];

        if (empty($value)) {
            $value = [['key' => '', 'value' => '']];
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
            $html[] = '<input ' . $this->app->jbhtml->buildAttrs([
                    'placeholder' => JText::_('JBZOO_JBKEYVALUE_KEY'),
                    'type'        => 'text',
                    'name'        => $this->_getName($controlName, $name) . '[' . $i . '][key]',
                    'value'       => isset($valueItem['key']) ? $valueItem['key'] : '',
                    'class'       => isset($class) ? $class : '',
                ]) . ' />';

            $html[] = '<strong>&nbsp;=&nbsp;</strong>';

            $html[] = '<input ' . $this->app->jbhtml->buildAttrs([
                    'placeholder' => JText::_('JBZOO_JBKEYVALUE_VALUE'),
                    'type'        => 'text',
                    'name'        => $this->_getName($controlName, $name) . '[' . $i . '][value]',
                    'value'       => isset($valueItem['value']) ? $valueItem['value'] : '',
                    'class'       => isset($class) ? $class : '',
                ]) . ' />';

            $html[] = '</div>';

            $i++;
        }

        $output = implode(PHP_EOL, $html);
        $output .= '<a href="#jbkeyvalue-add" class="jsKeyValueAdd">' . JText::_('JBZOO_JBKEYVALUE_ADD') . '</a>';

        return '<div class="jsKeyValue">' . $output . '</div>';
    }

    /**
     * Render itemOrder global
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function itemOrderGlobal($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        return $this->_global('itemOrder', $name, $value, $controlName, $node, $parent);
    }

    /**
     * Render itemOrder
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public function itemOrder($name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $customName = $this->_getName($controlName, $name);

        $value = (empty($value) || !is_array($value)) ? ['_jbzoo_none'] : $value;
        $allValues = array_chunk($value, 3);

        $html = [];
        foreach ($allValues as $index => $valueRow) {

            foreach ($valueRow as $key => $valueRowItem) {
                $valueRow[$key] = preg_replace('#_jbzoo_[0-9]_#i', '_jbzoo_' . $index . '_', $valueRowItem);
            }

            $html[] = $this->_renderItemOrderRow($valueRow, $customName, $index);
        }

        $html = array_filter($html);
        if (empty($html)) {
            $html[] = $this->_renderItemOrderRow(['_jbzoo_empty'], $customName);
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
     * @param     $rowValue
     * @param     $customName
     * @param int $index
     * @return null|string
     */
    protected function _renderItemOrderRow($rowValue, $customName, $index = 0)
    {
        $values = $this->app->data->create($this->app->jbarray->addToEachKey($rowValue, 'key_'));
        $options = $this->getSortElementsOptionList($index);

        $orderValue = $values->get('key_0');
        if (empty($orderValue) || preg_match('#_jbzoo(.*)none#i', $orderValue)) {
            return null;
        }

        $ctrl = [];
        $i = 0;

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_FIELD')
            . ': </span>' . JHtml::_('select.groupedlist', $options, $customName . '[]', [
                'list.attr'   => $this->app->jbhtml->buildAttrs([]),
                'list.select' => $values->get('key_' . $i++),
                'group.items' => null,
            ]);

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_AS')
            . ': </span>' . $this->app->jbhtml->select([
                '_jbzoo_' . $index . '_mode_s' => JText::_('JBZOO_SORT_AS_STRINGS'),
                '_jbzoo_' . $index . '_mode_n' => JText::_('JBZOO_SORT_AS_NUMBERS'),
                '_jbzoo_' . $index . '_mode_d' => JText::_('JBZOO_SORT_AS_DATES'),
            ], $customName . '[]', '', $values->get('key_' . $i++));

        $ctrl[] = '<span class="jbzoo-itemorder-label">' . JText::_('JBZOO_SORT_ORDER')
            . ': </span>' . $this->app->jbhtml->select([
                '_jbzoo_' . $index . '_order_asc'    => JText::_('JBZOO_SORT_ORDER_ASC'),
                '_jbzoo_' . $index . '_order_desc'   => JText::_('JBZOO_SORT_ORDER_DESC'),
                '_jbzoo_' . $index . '_order_random' => JText::_('JBZOO_SORT_ORDER_RANDOM'),
            ], $customName . '[]', '', $values->get('key_' . $i++));

        return '<div class="jbzoo-itemorder-row-field">'
            . implode("</div><div class=\"jbzoo-itemorder-row-field\">\n ", $ctrl)
            . '</div>';
    }

    /**
     * Get pre-prepared options list for itemorder list
     * @param int    $index
     * @param string $prefix
     * @param bool   $isModule
     * @return array
     */
    public function getSortElementsOptionList($index = 0, $prefix = '_jbzoo_<INDEX>', $isModule = false)
    {
        $stdFields = [
            'corepriority',
            'corename',
            'corealias',
            'corecreated',
            'corehits',
            'coremodified',
            'corepublish_down',
            'corepublish_up',
            'coreauthor',
        ];

        if ($prefix) {
            $prefix = str_replace('<INDEX>', $index, $prefix) . '_field_';
        }

        $excludeType = JBModelSearchindex::model()->getExcludeTypes();

        $typesPath = $this->app->path->path('jbtypes:');
        $files = Folder::files($typesPath, '.config');
        $app = $this->app->zoo->getApplication();

        // add std fields
        $coreGrp = JText::_('JBZOO_FIELDS_CORE');
        $options = [
            $coreGrp => [
                $prefix . '_none' => JText::_('JBZOO_FIELDS_CORE_NONE'),
                'random'          => JText::_('JBZOO_SORT_ORDER_RANDOM'),
            ]
        ];
        foreach ($stdFields as $stdField) {
            $options[$coreGrp][] = $this->_createOption($prefix . $stdField, 'JBZOO_FIELDS_CORE_' . $stdField);
        }

        // add custom fields
        foreach ($files as $file) {
            $fileContent = $this->app->jbfile->read($typesPath . '/' . $file);
            $typeData = json_decode($fileContent, true);

            $elements = [];
            if (!empty($typeData['elements'])) {
                foreach ($typeData['elements'] as $elementId => $element) {

                    if (strpos($elementId, '_') === 0 || in_array($element['type'], $excludeType, true)) {
                        continue;
                    }

                    if ($app) {
                        if ($type = $app->getType(JFile::stripExt($file))) {
                            $_element = $type->getElement($elementId);

                            if ($_element instanceof ElementJBPrice) {
                                $elements = array_merge($elements,
                                    $this->_getSortJBPriceOptionList($_element, $prefix, $isModule));
                            } else {
                                $elements[] = $this->_createOption($prefix . $elementId, $element['name'], false);
                            }
                        }
                    }

                }
            }

            $options[$typeData['name']] = $elements;
        }

        return $options;
    }

    /**
     * @param ElementJBPrice $element
     * @param string         $prefix
     * @param bool           $isModule
     * @return array
     */
    protected function _getSortJBPriceOptionList($element, $prefix, $isModule = false)
    {
        $list = [];

        $keyPrefix = $prefix . $element->identifier;
        $elements = $element->getElements();
        if (count($elements)) {
            foreach ($elements as $element) {
                $type = StringHelper::ucfirst($element->getElementType());

                if ($isModule && strpos($element->identifier, '_') === 0 && !in_array($element->identifier,
                        ['_value', '_sku'])) {
                    continue;
                }

                if ($element->identifier == '_image') {
                    continue;
                }

                $list[] = $this->_createOption($keyPrefix . '__' . $element->identifier,
                    $element->getName() . ' - ' . $type, false);
            }
        }

        return $list;
    }

    /**
     * Check is current
     * @param SimpleXMLElement $node
     * @param AppParameterForm $parent
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
     * @param array            $optionsList
     * @param string           $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @return mixed
     */
    protected function _renderRadio($optionsList, $value, $controlName, SimpleXMLElement $node)
    {
        $html = [];
        foreach ($optionsList as $key => $option) {
            $id = 'radio-' . $this->app->jbstring->getId();
            $attributes = [
                'id'    => $id,
                'type'  => 'radio',
                'name'  => $controlName,
                'value' => $key,
            ];

            if ($key == $value) {
                $attributes = array_merge($attributes, ['checked' => 'checked']);
            }

            $html[] = '<input ' . $this->app->jbhtml->buildAttrs($attributes) . ' /> '
                . '<label ' . $this->app->jbhtml->buildAttrs(['for' => $id]) . '>'
                . JText::_($option)
                . '</label>';
        }

        return implode(" \n", $html);
    }

    /**
     * Render layout list
     * @param string           $method
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    protected function _global($method, $name, $value, $controlName, SimpleXMLElement $node, $parent)
    {
        $this->app->document->addScript('fields:global.js');

        $id = 'listglobal-' . $this->app->jbstring->getId();
        $global = $parent->getValue((string)$name) === null;

        $html = [];
        $html[] = '<div class="global list">';
        $html[] = '<input id="' . $id . '" type="checkbox"' . ($global ? ' checked="checked"' : '') . ' />';
        $html[] = '<label for="' . $id . '">' . JText::_('Global') . '</label>';
        $html[] = '<div class="input">';
        $html[] = call_user_func_array(
            [$this, $method],
            [$name, $value, $controlName, $node, $parent]
        );
        $html[] = '</div></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Render list params
     * @param array            $optionsList
     * @param string           $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param bool             $translate
     * @return mixed
     */
    protected function _renderList($optionsList, $value, $controlName, SimpleXMLElement $node, $translate = true)
    {
        $attributes = [];
        if ($this->_getAttr($node, 'multiple', '0') == '1') {
            $attributes['multiple'] = 'multiple';
            $attributes['size'] = $this->_getAttr($node, 'size', '10');
            $controlName .= '[]';
        }

        $attributes['class'] = $this->_getAttr($node, 'class', 'inputbox');

        $options = $this->app->html->listOptions($optionsList);

        return $this->app->html->genericList($options, $controlName, $attributes, 'value', 'text', $value, false,
            $translate);
    }

    /**
     * @param SimpleXMLElement $node
     * @param string           $attrName
     * @param mixed            $default
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
     * @param $controlName
     * @param $name
     * @return string
     */
    protected function _getName($controlName, $name)
    {
        return $controlName . '[' . $name . ']';
    }

    /**
     * Create option instance
     * @param string $key
     * @param string $value
     * @param bool   $translate
     * @return mixed
     */
    protected function _createOption($key, $value, $translate = true)
    {
        $name = $translate ? JText::_($value) : $value;

        return JHtml::_('select.option', $key, $name, 'value', 'text');
    }

}
