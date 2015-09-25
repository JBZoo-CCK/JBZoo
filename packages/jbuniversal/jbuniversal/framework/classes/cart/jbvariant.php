<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartVariant
 * @since 2.2
 */
class JBCartVariant extends ArrayObject
{
    /**
     * Id of variant
     * @type int
     */
    protected $id = 0;

    /**
     * Array of objects elements
     * @type array
     */
    protected $elements = array();

    /**
     * Hash based on simple elements and their values.
     * @type string
     */
    protected $hash;

    /**
     * @type JBCartValue
     */
    public $total;

    /**
     * @type JBCartValue
     */
    public $price;

    /**
     * @type JBCartVariantList
     */
    protected $list;

    /**
     * Class constructor
     * @param JBCartVariantList $list
     * @param array             $elements
     * @param array             $options
     */
    public function __construct(array $elements = array(), array $options = array(), JBCartVariantList $list = null)
    {
        // set variant id
        if (isset($options['id'])) {
            $this->setId($options['id']);

            unset($options['id']);
        }

        // set variant list if exists
        if ($list instanceof JBCartVariantList) {
            $this->setList($list);
        }

        //Bind elements
        if ($elements) {
            $data = new AppData(isset($options['data']) ? $options['data'] : array());

            $this->add($elements, $data);

            unset($options['data']);
        }

        //set elements data
        if (isset($options)) {
            $this->bindData($options);
        }

        parent::__construct($this->elements);
    }

    /**
     * Get element by identifier
     * @param string $id
     * @param mixed  $default
     * @return JBCartElementPrice|mixed
     */
    public function get($id, $default = null)
    {
        return isset($this->elements[$id]) ? $this->elements[$id] : $default;
    }

    /**
     * @param integer        $id Element identifier.
     * @param ElementJBPrice $element
     * @throws JBCartVariantException
     */
    public function set($id, $element)
    {
        if (!$element instanceof JBCartElementPrice) {
            throw new JBCartVariantException('In Method: ' . __FUNCTION__ . ' values of array must be an instance of JBCartElementPrice.');
        }

        $this->elements[$id] = $element;
    }

    /**
     * @param array         $elements
     * @param AppData|array $options
     */
    public function add(array $elements, AppData $options)
    {
        foreach ($elements as $element) {
            $this->set($element->id(), $this->setElement($element, (array)$options->get($element->id())));
        }
    }

    /**
     * Check if JBCartVariant exists.
     * @param  string $id Element identifier
     * @return bool
     */
    public function has($id)
    {
        return isset($this->elements[$id]) || array_key_exists($id, $this->elements);
    }

    /**
     * Get all elements.
     * @return array
     */
    public function all()
    {
        return $this->elements;
    }

    /**
     * @param  string $id
     * @return bool
     */
    public function isCore($id)
    {
        return $this->has($id) && ($this->get($id)->isCore());
    }

    /**
     * @param callable $p
     * @return bool
     */
    public function exists(Closure $p)
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Closure $callback
     * @return static
     */
    public function filter(Closure $callback)
    {
        return new static(array_filter($this->elements, $callback));
    }

    /**
     * @param Closure $callback
     * @return static
     */
    public function map(Closure $callback)
    {
        return new static(array_map($callback, $this->elements));
    }

    /**
     * @param string $group Group of elements to count
     * @return int
     */
    public function count($group = 'all')
    {
        if ($group === 'all') {
            $count = count($this->elements);

        } elseif ($group === 'core') {
            $count = $this->filter(function ($element) {
                return $element->isCore();
            })->count();

        } else {
            $count = $this->filter(function ($element) {
                return !$element->isCore();
            })->count();;
        }

        return $count;
    }

    /**
     * Get JBCartVariant id
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        if ($this->id !== (int)$id) {
            $this->id = (int)$id;
        }

        return $this;
    }

    /**
     * Return hash for variant base on elements and theirs values.
     * @return string
     */
    public function hash()
    {
        if ($this->hash !== null) {
            return $this->hash;
        }
        $hash = (array)array_filter(array_map(function ($element) {
            $value = $element->getValue(true);

            return (!$element->isCore() && ($value == '0' || !empty($value)))
                ? (array)$element->data()
                : null;
        }, $this->all()));
        asort($hash);

        $this->hash = md5(serialize($hash));

        return $this->hash;
    }

    /**
     *  Clear all data
     */
    public function clear()
    {
        $this->elements = array();
        $this->total    = null;
        $this->price    = null;
        $this->hash     = null;
        $this->id       = null;
    }

    /**
     * Set link to list of variations.
     * @param JBCartVariantList $list
     * @return JBCartVariant
     * @throws JBCartVariantException
     */
    public function setList($list)
    {
        if (!$list instanceof JBCartVariantList) {
            throw new JBCartVariantException('In Method: ' . __FUNCTION__ . ' first argument must be an instance of JBCartVariantList.');
        }
        $this->list = $list;

        return $this;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Check if variant is basic
     * @return bool
     */
    public function isBasic()
    {
        return $this->id === 0;
    }

    /**
     * Check if variant key equals needle
     * @param  int $id Needle id
     * @return bool
     */
    public function is($id)
    {
        return $this->id === (int)$id;
    }

    /**
     * @param bool   $toString
     * @param string $key - element identifier
     * @param mixed  $default
     * @return JBCartElementPrice|JBCartValue
     */
    public function getValue($toString = false, $key, $default = null)
    {
        if ($element = $this->get($key)) {
            return $element->getValue($toString);
        }

        return $default;
    }

    /**
     * @param string $id    Element identifier
     * @param mixed  $value Value to check
     * @return bool
     */
    public function hasOption($id, $value)
    {
        $element = $this->get($id);

        $element->bindData($value);
        if ($element->hasOption($element->getValue(true))) {
            return $value;
        }

        return false;
    }

    /**
     * Get all core elements from variant.
     * @return array
     */
    public function getCore()
    {
        return array_filter($this->all(), function ($element) {
            return $element->isCore() ? $element : null;
        });
    }

    /**
     * Get all simple elements from variant.
     * @return array
     */
    public function getSimple()
    {
        return array_filter($this->all(), function ($element) {
            return !$element->isCore() ? $element : null;
        });
    }

    /**
     * Get all required elements.
     * @return array
     */
    public function getRequired()
    {
        return array_filter($this->all(), function ($element) {
            return $element->isRequired() ? $element : null;
        });
    }

    /**
     * Get data from all elements
     * @param null $isOverlay
     * @return array
     */
    public function data($isOverlay = null)
    {
        $elements = $this->isBasic() ? $this->getCore() : $this->all();
        $result   = array_filter(array_map(function ($element) {
            $value = JString::trim($element->getValue(true));

            return (!empty($value) || $value == '0') ? (array)$element->data() : null;
        }, $elements));

        // hack for unique props
        if ($isOverlay === true) {
            $isFound = 0;
            foreach ($result as $key => $item) {
                if (strpos($key, '_') !== 0) {
                    if ($isFound != 0) {
                        unset($result[$key]);
                    }
                    $isFound++;
                }
            }
        }

        return $result;
    }


    /**
     * @param array $options
     * @return $this
     */
    public function bindData(array $options = array())
    {
        $elements = new AppData();
        if (isset($options['data'])) {
            $elements->exchangeArray($options['data']);
        }

        foreach ($this->elements as $key => $element) {
            $this->set($key, $this->setElement($element, $elements->get($key)));
        }

        return $this;
    }

    /**
     * Get Total price for variant
     * @return JBCartValue
     * @throws Exception
     */
    public function getTotal()
    {
        if ($this->total !== null) {
            return $this->total;
        }

        if (!$this->list->isOverlay) {

            $total = $this->getPrice()->minus($this->getValue(true, '_discount'), true);

            if ($this->list instanceof JBCartVariantList) {
                $total = $this->list->addModifiers($total, true);
            }
            $this->total = $total;

            return $this->total;
        }

        throw new JBCartVariantException('Variant cant calculate his own total price. Please use JBCartVariantList');
    }

    /**
     * Get price for variant
     * @return JBCartValue
     */
    public function getPrice()
    {
        if ($this->price === null) {

            $price = JBCart::val();
            if ($element = $this->get('_value')) {
                $price->set($element->getValue(true));
                if ($this->list->isOverlay === false && $element->isModifier() && !$this->isBasic()) {
                    $price = $this->list->first()->getValue(false, '_value')->add($price);
                }
            }

            $this->price = $price;
            if ($this->list->isOverlay === false) {
                $this->price = $price->add($this->getValue(true, '_margin'), true);

                if ($this->list instanceof JBCartVariantList) {
                    $this->price = $this->list->addModifiers($this->price, false);
                }
            }
        }

        return $this->price->getClone();
    }

    /**
     * Check if item in stock
     * @param integer $quantity
     * @return bool
     */
    public function inStock($quantity)
    {
        /** @type JBCartElementPriceBalance $element */
        if ($element = $this->get('_balance')) {
            if ($element->inStock($quantity)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * If someone is accessed as a string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     * @return mixed
     */
    protected function setElement($element, $data)
    {
        if (is_array($data) && !empty($data)) {
            $data = array_filter($data, function ($value) {
                ;
                return ($value !== '' && $value !== null);
            });
        }
        $element->setVariant($this->id);

        if ($this->list instanceof JBCartVariantList && !$this->isBasic()) {
            $basic = $this->list->first()->getValue(true, $element->identifier);

            if ($basic !== null && $basic !== '') {
                $data['_basic'] = $basic;
            }
        }

        if (!empty($data)) {
            $element->bindData($data);
        }

        return $element;
    }
}

/**
 * Class JBCartVariantException
 */
class JBCartVariantException extends AppException
{
}