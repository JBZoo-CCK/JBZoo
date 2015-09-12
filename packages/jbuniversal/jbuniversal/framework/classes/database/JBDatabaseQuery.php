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
 * Class JBDatabaseQuery
 */
class JBDatabaseQuery
{

    /**
     * @var JDatabase|null
     */
    protected $db = null;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $element = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $select = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $delete = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $update = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $insert = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $replace = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $from = null;

    /**
     * @var array
     */
    protected $join = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $set = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $where = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $group = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $having = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $columns = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $values = null;

    /**
     * @var JBDatabaseQueryElement|null
     */
    protected $order = null;

    /**
     * @var JBDatabaseQueryElement
     */
    protected $limit = null;

    /**
     * @var string
     */
    protected $name_quotes = '`';

    /**
     * @var string
     */
    protected $null_date = '0000-00-00 00:00:00';

    /**
     * Class constructor
     * @param   JDatabase $db The database connector resource
     * @return  JBDatabaseQuery
     */
    public function __construct(JDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * Concatenates an array of column names or values.
     * @param   array  $values    An array of values to concatenate.
     * @param   string $separator As separator to place between each value.
     * @return  string
     */
    function concat($values, $separator = null)
    {
        if ($separator) {
            $concat_string = 'CONCAT_WS(' . $this->quote($separator);

            foreach ($values as $value) {
                $concat_string .= ', ' . $value;
            }

            return $concat_string . ')';
        } else {
            return 'CONCAT(' . implode(',', $values) . ')';
        }
    }

    /**
     * Where conditions
     * @param string         $conditions
     * @param boolean|string $value
     * @param string         $logic
     * @return JBDatabaseQuery
     */
    public function where($conditions, $value = null, $logic = 'AND')
    {

        if ($conditions === null) {
            return $this;
        }

        $conditions = $this->_clearData($conditions, $value);

        if (is_null($this->where)) {
            $this->where = new JBDatabaseQueryElement('WHERE', $conditions, ' ');

        } else {
            $this->where->append($logic . ' ' . $conditions);

        }

        return $this;
    }

    /**
     * Set query limit
     * @param int $length
     * @param int $offset
     * @return JBDatabaseQuery
     */
    public function limit($length, $offset = 0)
    {
        $conditions = false;
        if ($offset) {
            $conditions = (int)$offset . ', ' . (int)$length;

        } else {
            if ($length) {
                $conditions = (int)$length;
            }
        }

        if ($conditions) {
            $this->limit = new JBDatabaseQueryElement('LIMIT', $conditions);
        }

        return $this;
    }

    /**
     * Casts a value to a char.
     * Ensure that the value is properly quoted before passing to the method.
     * @param   string $value The value to cast as a char.
     * @return  string  Returns the cast value.
     */
    public function castAsChar($value)
    {
        return $value;
    }

    /**
     *
     * /**
     * Gets the number of characters in a string.
     * Note, use 'length' to find the number of bytes in a string.
     * @param string $field A value
     * @return string
     */
    public function charLength($field)
    {
        return 'CHAR_LENGTH(' . $field . ')';
    }

    /**
     * Adds a column, or array of column names that would be used for an INSERT INTO statement.
     * @param   mixed $columns A column name, or array of column names.
     * @return  JBDatabaseQuery
     */
    function columns($columns)
    {
        if (is_null($this->columns)) {
            $this->columns = new JBDatabaseQueryElement('()', $columns);
        } else {
            $this->columns->append($columns);
        }

        return $this;
    }

    /**
     * Gets the current date and time.
     * @return  string
     */
    function currentTimestamp()
    {
        return 'CURRENT_TIMESTAMP()';
    }

    /**
     * Returns a PHP date() function compliant date format for the database driver.
     * @return  string  The format string.
     */
    public function dateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * Add a table name to the DELETE clause of the query.
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * @param   string $table The name of the table to delete from.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function delete($table = null)
    {
        $this->type   = 'delete';
        $this->delete = new JBDatabaseQueryElement('DELETE', null);

        if (!empty($table)) {
            $this->from($table);
        }

        return $this;
    }

    /**
     * Method to escape a string for usage in an SQL statement.
     * @param   string $text  The string to be escaped.
     * @param   bool   $extra Optional parameter to provide extra escaping.
     * @return  string  The escaped string.
     */
    public function escape($text, $extra = false)
    {
        $this->db->escape($text, $extra);
    }

    /**
     * Add a table to the FROM clause of the query.
     * Note that while an array of tables can be provided, it is recommended you use explicit joins.
     * @param   mixed $tables A string or array of table names.
     * @param   mixed $asName Table name
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function from($tables, $asName = null)
    {
        if ($asName) {
            $tables .= ' AS ' . $asName;
        }

        $this->from = new JBDatabaseQueryElement('FROM', $tables);
        return $this;
    }

    /**
     * Add a grouping column to the GROUP clause of the query.
     * @param   mixed $columns A string or array of ordering columns.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function group($columns)
    {
        if (is_null($this->group)) {
            $this->group = new JBDatabaseQueryElement('GROUP BY', $columns);
        } else {
            $this->group->append($columns);
        }

        return $this;
    }

    /**
     * A conditions to the HAVING clause of the query.
     * @param string         $conditions
     * @param boolean|string $value
     * @param string         $logic
     * @return JBDatabaseQuery
     */
    public function having($conditions, $value = null, $logic = 'AND')
    {
        $conditions = $this->_clearData($conditions, $value);

        if (is_null($this->having)) {
            $this->having = new JBDatabaseQueryElement('HAVING', $conditions, ' ');

        } else {
            $this->having->append($logic . ' ' . $conditions);

        }

        return $this;
    }


    /**
     * Add an INNER JOIN clause to the query.
     * @param   string $conditions A string or array of conditions.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function innerJoin($conditions)
    {
        $this->join('INNER', $conditions);
        return $this;
    }

    /**
     * Add a table name to the INSERT clause of the query.
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * @param   mixed $table The name of the table to insert data into.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function insert($table)
    {
        $this->type   = 'insert';
        $this->insert = new JBDatabaseQueryElement('INSERT INTO', '`' . $table . '`');

        return $this;
    }

    /**
     * Add a table name to the REPLACE clause of the query.
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * @param   mixed $table The name of the table to insert data into.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function replace($table)
    {
        $this->type    = 'replace';
        $this->replace = new JBDatabaseQueryElement('REPLACE INTO', '`' . $table . '`');

        return $this;
    }

    /**
     * Add a JOIN clause to the query.
     * @param   string $type       The type of join. This string is prepended to the JOIN keyword.
     * @param   string $conditions A string or array of conditions.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function join($type, $conditions)
    {
        if (is_null($this->join)) {
            $this->join = array();
        }

        $this->join[] = new JBDatabaseQueryElement(strtoupper($type) . ' JOIN', $conditions);

        return $this;
    }

    /**
     * Add a LEFT JOIN clause to the query.
     * @param $conditions
     * @return JBDatabaseQuery
     */
    public function leftJoin($conditions)
    {
        $this->join('LEFT', $conditions);
        return $this;
    }

    /**
     * Get the length of a string in bytes.
     * Note, use 'charLength' to find the number of characters in a string.
     * @param   string $value The string to measure.
     * @return  string
     */
    function length($value)
    {
        return 'LENGTH(' . $value . ')';
    }

    /**
     * Get the null or zero representation of a timestamp for the database driver.
     * @param   boolean $quoted Optionally wraps the null date in database quotes (true by default).
     * @return  string  Null or zero representation of a timestamp.
     */
    public function nullDate($quoted = true)
    {
        $result = $this->db->getNullDate($quoted);

        if ($quoted) {
            return $this->db->quote($result);
        }

        return $result;
    }

    /**
     * Add a ordering column to the ORDER clause of the query.
     * @param   mixed $columns A string or array of ordering columns.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function order($columns)
    {
        if (is_null($this->order)) {
            $this->order = new JBDatabaseQueryElement('ORDER BY', $columns);
        } else {
            $this->order->append($columns);
        }

        return $this;
    }

    /**
     * Add an OUTER JOIN clause to the query.
     * @param   string $conditions A string or array of conditions.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function outerJoin($conditions)
    {
        $this->join('OUTER', $conditions);
        return $this;
    }

    /**
     * Method to quote and optionally escape a string to database requirements for insertion into the database.
     * @param   string $text   The string to quote.
     * @param   bool   $escape True to escape the string, false to leave it unchanged.
     * @return  string  The quoted input string.
     */
    public function quote($text, $escape = true)
    {

        if (version_compare(JVERSION, '1.5.0', '>')) {
            $escape = false;
        } else {
            $escape = true;
        }

        return $this->db->quote(($escape ? $this->db->escape($text) : $text));
    }

    /**
     * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
     * risks and reserved word conflicts.
     * @param   string $name The identifier name to wrap in quotes.
     * @return  string  The quote wrapped name.
     */
    public function quoteName($name)
    {
        return $this->db->quoteName($name);
    }

    /**
     * Add a RIGHT JOIN clause to the query.
     * @param   string $conditions A string or array of conditions.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function rightJoin($conditions)
    {
        $this->join('RIGHT', $conditions);
        return $this;
    }

    /**
     * Add a single column, or array of columns to the SELECT clause of the query.
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * The select method can, however, be called multiple times in the same query.
     * @param   mixed $columns A string or an array of field names.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function select($columns)
    {
        $this->type = 'select';

        if (is_null($this->select)) {
            $this->select = new JBDatabaseQueryElement('SELECT', $columns);
        } else {
            $this->select->append($columns);
        }

        return $this;
    }

    /**
     * Add a single condition string, or an array of strings to the SET clause of the query.
     * @param mixed $conditions A string or array of conditions.
     * @param null  $value      Value foe set
     * @return JBDatabaseQuery
     */
    public function set($conditions, $value = null)
    {
        $conditions = $this->_clearData($conditions, $value);

        if (is_null($this->set)) {
            $this->set = new JBDatabaseQueryElement('SET', $conditions, ' ');

        } else {
            $this->set->append(', ' . $conditions);

        }

        return $this;
    }

    /**
     * Add a table name to the UPDATE clause of the query.
     * Note that you must not mix insert, update, delete and select method calls when building a query.
     * @param   mixed $tables A string or array of table names.
     * @return  JBDatabaseQuery  Returns this object to allow chaining.
     */
    public function update($tables)
    {
        $this->type   = 'update';
        $this->update = new JBDatabaseQueryElement('UPDATE', $tables);

        return $this;
    }

    /**
     * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
     * @param string $column A single tuple, or array of tuples.
     * @param string $value  Value for insert
     * @return JBDatabaseQuery
     */
    function values($column, $value)
    {
        if (is_null($this->values)) {
            $this->values  = new JBDatabaseQueryElement('()', $this->quote($value), ', ');
            $this->columns = new JBDatabaseQueryElement('()', '`' . $column . '`');

        } else {
            $this->values->append($this->quote($value));
            $this->columns->append('`' . $column . '`');
        }

        return $this;
    }

    /**
     * Clear and
     * @param string      $conditions
     * @param null|string $value
     * @return string
     */
    protected function _clearData($conditions, $value = null)
    {

        if ($value) {
            $value = $this->quote($value);
        }

        $conditions = trim($conditions);
        $conditions = preg_replace('#\?#', $value, $conditions) . PHP_EOL;
        return $conditions;
    }

    /**
     * Magic function to convert the query to a string.
     * @return  string    The completed query.
     */
    public function __toString()
    {
        $query = '';

        switch ($this->type) {
            case 'element':
                $query .= (string)$this->element;
                break;

            case 'select':

                if (is_null($this->select)) {
                    $this->select = '*';
                }

                $query .= (string)$this->select;
                $query .= (string)$this->from;

                if ($this->join) {
                    $this->join = array_unique($this->join);
                    foreach ($this->join as $join) {
                        $query .= (string)$join . PHP_EOL;
                    }
                }

                if ($this->where) {
                    $query .= (string)$this->where;
                }

                if ($this->group) {
                    $query .= (string)$this->group;
                }

                if ($this->having) {
                    $query .= (string)$this->having;
                }

                if ($this->order) {
                    $query .= (string)$this->order;
                }

                break;

            case 'delete':
                $query .= (string)$this->delete;
                $query .= (string)$this->from;

                if ($this->join) {
                    $this->join = array_unique($this->join);
                    foreach ($this->join as $join) {
                        $query .= (string)$join;
                    }
                }

                if ($this->where) {
                    $query .= (string)$this->where;
                }

                break;

            case 'update':
                $query .= (string)$this->update;
                $query .= (string)$this->set;

                if ($this->where) {
                    $query .= (string)$this->where;
                }

                break;

            case 'insert':
                $query .= (string)$this->insert;

                // Set method
                if ($this->set) {
                    $query .= (string)$this->set;
                } elseif ($this->values) {
                    if ($this->columns) {
                        $query .= (string)$this->columns;
                    }

                    $query .= ' VALUES ';
                    $query .= (string)$this->values;
                }

                break;

            case 'replace':
                $query .= (string)$this->replace;

                // Set method
                if ($this->set) {
                    $query .= (string)$this->set;
                } elseif ($this->values) {
                    if ($this->columns) {
                        $query .= (string)$this->columns;
                    }

                    $query .= ' VALUES ';
                    $query .= (string)$this->values;
                }

                break;
        }

        if ($this->limit) {
            $query .= (string)$this->limit;
        }

        $query = str_replace('#__', $this->db->getPrefix(), $query);

        return $query;
    }

    /**
     * Clear data from the query or a specific clause of the query.
     * @param null|string $clause Optionally, the name of the clause to clear, or nothing to clear the whole query.
     * @return JBDatabaseQuery
     */
    public function clear($clause = null)
    {
        switch ($clause) {
            case 'select':
                $this->select = null;
                $this->type   = null;
                break;

            case 'delete':
                $this->delete = null;
                $this->type   = null;
                break;

            case 'update':
                $this->update = null;
                $this->type   = null;
                break;

            case 'insert':
                $this->insert = null;
                $this->type   = null;
                break;

            case 'replace':
                $this->replace = null;
                $this->type    = null;
                break;

            case 'from':
                $this->from = null;
                break;

            case 'join':
                $this->join = null;
                break;

            case 'set':
                $this->set = null;
                break;

            case 'where':
                $this->where = null;
                break;

            case 'group':
                $this->group = null;
                break;

            case 'having':
                $this->having = null;
                break;

            case 'order':
                $this->order = null;
                break;

            case 'columns':
                $this->columns = null;
                break;

            case 'values':
                $this->values = null;
                break;

            case 'limit':
                $this->limit = null;
                break;

            default:
                $this->type    = null;
                $this->select  = null;
                $this->delete  = null;
                $this->update  = null;
                $this->insert  = null;
                $this->replace = null;
                $this->from    = null;
                $this->join    = null;
                $this->set     = null;
                $this->where   = null;
                $this->group   = null;
                $this->having  = null;
                $this->order   = null;
                $this->columns = null;
                $this->values  = null;
                $this->limit   = null;
                break;
        }

        return $this;
    }

}
