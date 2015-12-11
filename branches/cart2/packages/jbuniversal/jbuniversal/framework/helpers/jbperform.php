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
 * Class JBAjaxHelper
 */
class JBPerformHelper extends AppHelper
{
    const TEST_COUNT = 1;
    const TEST_TABLE = 'jbzoo_performance_test';

    /**
     * @var mysqli
     */
    protected $_dbLink = null;

    /**
     * @var JBSessionHelper
     */
    protected $_jbsession = null;

    /**
     * @var string
     */
    protected $_sessionGroup = 'benchmark';

    /**
     * @var string
     */
    protected $_testEmail = 'hosting-test@jbzoo.com';

    /**
     * Constructor
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_jbsession = $this->app->jbsession;
        $this->app->jbenv->maxperformance();
    }

    /**
     * Execute test by name
     * @param string $testName
     * @return array
     */
    public function execTest($testName)
    {
        $actionName = '_test' . str_replace('_', '', $testName);

        if ($testName == 'engine_init') {
            $this->_jbsession->clearGroup($this->_sessionGroup);
        }

        if (method_exists($this, $actionName)) {

            //run tests
            $values = array();
            for ($j = 0; $j < self::TEST_COUNT; $j++) {

                $st     = microtime(true);
                $result = call_user_func(array($this, $actionName));
                $fin    = microtime(true);

                if (is_null($result)) {
                    $values[] = $fin - $st;
                } else {
                    $values[] = $result;
                }

                if (self::TEST_COUNT > 1) {
                    sleep(1); // without very hard test!
                }
            }

            if (strpos($testName, 'version') === 0 || $testName == 'system_loadavg') { // hack
                reset($values);
                $value = current($values);
            } else {
                $value = array_sum($values) / count($values);
            }

            $this->_jbsession->set($testName, $value, $this->_sessionGroup);

            // set output format
            return array(
                'value' => $this->toFormat($value, $testName),
                'alert' => $this->isAlert($value, $testName),
            );
        }

        return array();
    }

    /**
     * Open own DB conect
     * @return mysqli
     */
    protected function _DbConnect()
    {
        if (is_null($this->_dbLink)) {
            $config = new JConfig();

            $this->_dbLink = mysqli_connect($config->host, $config->user, $config->password, $config->db);
        }
    }

    /**
     * Clear query to DB without
     * @param $query
     * @return bool|mysqli_result
     */
    protected function _DbQuery($query)
    {
        return mysqli_query($this->_dbLink, $query);
    }

    /**
     * Close own DB conect
     * @return bool
     */
    protected function _DbClose()
    {
        if (!is_null($this->_dbLink)) {
            mysqli_close($this->_dbLink);
            $this->_dbLink = null;
        }

        return false;
    }

    /**
     * Create temporary table for tests
     */
    protected function _DbCreateTemp($isBx = false)
    {
        $this->_DbConnect();
        $this->_DbQuery('DROP TABLE IF EXISTS `' . self::TEST_TABLE . '`');
        if ($isBx) {
            $this->_DbQuery('CREATE TABLE `' . self::TEST_TABLE . '` (
                `ID` INT(18) NOT NULL AUTO_INCREMENT,
                `REFERENCE_ID` INT(18) NULL DEFAULT NULL,
                `NAME` VARCHAR(200) NULL DEFAULT NULL,
                PRIMARY KEY (`ID`),
                INDEX `IX_B_PERF_TEST_0` (`REFERENCE_ID`)
            ) COLLATE=\'utf8_general_ci\' ENGINE=MyISAM');
        } else {
            $this->_DbQuery('CREATE TABLE `' . self::TEST_TABLE . '`(i INT)  COLLATE=\'utf8_general_ci\' ENGINE=MyISAM');
        }
    }

    /**
     * Create temporary table for tests
     */
    protected function _DbRemoveTemp()
    {
        $this->_DbQuery('DROP TABLE IF EXISTS `' . self::TEST_TABLE . '`');
        $this->_DbClose();
    }

    /**
     * CPU complex test
     */
    protected function _testCpuComplex()
    {
        $N1 = $N2 = $k = 0;

        $s1 = microtime(true);
        for ($i = 0; $i < 1000000; $i++) {
            // noop
        }
        $e1 = microtime(true);
        $N1 = $e1 - $s1;

        $s2 = microtime(true);
        for ($i = 0; $i < 1000000; $i++) {
            //This is one op
            $k++;
            $k--;
            $k++;
            $k--;
        }
        $e2 = microtime(true);
        $N2 = $e2 - $s2;

        if ($N2 > $N1) {
            return 1 / ($N2 - $N1);
        }

        return 0;
    }

    /**
     * CPU sin test
     */
    protected function _testCpuSin()
    {
        for ($i = 1; $i < 1000000; $i++) {
            $a = sin($i);
        }
    }

    /**
     * CPU sin test
     */
    protected function _testCpuConcatDot()
    {
        $a = $b = "";
        for ($i = 1; $i < 1000000; $i++) {
            $c = $a . $b;
        }
    }

    /**
     * CPU sin test
     */
    protected function _testCpuConcatQuotes()
    {
        $a = $b = "";
        for ($i = 1; $i < 1000000; $i++) {
            $c = "$a$b";
        }
    }

    /**
     * CPU sin test
     */
    protected function _testCpuConcatArray()
    {
        $a = $b = "";
        for ($i = 1; $i < 1000000; $i++) {
            implode("", array($a, $b));
        }
    }

    /**
     * MySQL connection test
     */
    protected function _testMysqlConnect()
    {
        $st = microtime(true);
        $this->_DbConnect();
        $fin = microtime(true);

        $this->_DbClose();

        return $fin - $st;
    }

    /**
     * MySQL sin test
     */
    protected function _testMysqlSin()
    {
        $this->_DbConnect();

        $st = microtime(true);
        $this->_DbQuery('SELECT BENCHMARK(1000000, (select sin(100)))');
        $fin = microtime(true);

        $this->_DbClose();

        return $fin - $st;
    }

    /**
     * Mysql Insert test
     */
    protected function _testMysqlInsert()
    {
        // prepare
        $this->_DbCreateTemp();

        $st = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            $this->_DbQuery('INSERT INTO `' . self::TEST_TABLE . '` values (' . $i . ')');
        };
        $fin = microtime(true);

        // drop & close
        $this->_DbRemoveTemp();

        return $fin - $st;
    }

    /**
     * Mysql Insert test
     */
    protected function _testMysqlSelect()
    {
        // prepare
        $this->_DbCreateTemp();

        // add entries
        for ($i = 0; $i < 10000; $i++) {
            $this->_DbQuery('INSERT INTO `' . self::TEST_TABLE . '` VALUES (' . $i . ')');
        };

        $st     = microtime(true);
        $result = $this->_DbQuery('SELECT * FROM `' . self::TEST_TABLE . '` WHERE i > 0');
        while ($row = mysqli_fetch_assoc($result)) ;
        $fin = microtime(true);

        // drop & close
        $this->_DbRemoveTemp();

        return $fin - $st;
    }

    /**
     * Mysql Insert test
     */
    protected function _testMysqlSelectAdvance()
    {
        // prepare
        $this->_DbCreateTemp(true);

        $db = JFactory::getDbo();

        $strSql = "INSERT INTO `" . self::TEST_TABLE . "` (REFERENCE_ID, NAME) values (#i#-1, '" . str_repeat("x", 200) . "')";
        for ($i = 0; $i < 100; $i++) {
            $this->_DbQuery(str_replace("#i#", $i, $strSql));
        }

        $strSql = 'SELECT * FROM `' . self::TEST_TABLE . '` WHERE ID = #i#';

        for ($j = 0; $j < 4; $j++) {
            $N1 = $N2 = 0;

            $s1 = microtime(true);
            for ($i = 0; $i < 100; $i++) {
                $sql = str_replace("#i#", $i, $strSql);
            }
            $e1 = microtime(true);
            $N1 = $e1 - $s1;

            $s2 = microtime();
            for ($i = 0; $i < 100; $i++) {
                mysqli_fetch_assoc($this->_DbQuery(str_replace("#i#", $i, $strSql)));
            }
            $e2 = microtime();
            $N2 = $e2 - $s2;

            if ($N2 > $N1) {
                $res[] = 100 / ($N2 - $N1);
            }
        }

        $this->_DbRemoveTemp();

        if (count($res)) {
            return array_sum($res) / doubleval(count($res));
        } else {
            return 0;
        }
    }

    /**
     * Mysql Insert test
     */
    protected function _testMysqlReplaceAdvance()
    {
        // prepare
        $this->_DbCreateTemp(true);

        $db = JFactory::getDbo();

        $strSql = "INSERT INTO `" . self::TEST_TABLE . "` (REFERENCE_ID, NAME) values (#i#-1, '" . str_repeat("x", 200) . "')";
        for ($i = 0; $i < 100; $i++) {
            $this->_DbQuery(str_replace("#i#", $i, $strSql));
        }

        $strSql = "UPDATE `" . self::TEST_TABLE . "` SET REFERENCE_ID = ID+1, NAME = '" . str_repeat("y", 200) . "' WHERE ID = #i#";

        for ($j = 0; $j < 4; $j++) {
            $N1 = $N2 = 0;

            $s1 = microtime(true);
            for ($i = 0; $i < 100; $i++) {
                $sql = str_replace("#i#", $i, $strSql);
            }
            $e1 = microtime(true);
            $N1 = $e1 - $s1;

            $s2 = microtime();
            for ($i = 0; $i < 100; $i++) {
                $this->_DbQuery(str_replace("#i#", $i, $strSql));
            }
            $e2 = microtime();
            $N2 = $e2 - $s2;

            if ($N2 > $N1) {
                $res[] = 100 / ($N2 - $N1);
            }
        }

        $this->_DbRemoveTemp();

        if (count($res)) {
            return array_sum($res) / doubleval(count($res));
        } else {
            return 0;
        }
    }

    /**
     * @return float
     */
    protected function _testMysqlInsertAdvance()
    {
        // prepare
        $this->_DbCreateTemp(true);

        $db = JFactory::getDbo();

        $strSql = "INSERT INTO `" . self::TEST_TABLE . "` (REFERENCE_ID, NAME) values (#i#-1, '" . str_repeat("x", 200) . "')";

        for ($j = 0; $j < 4; $j++) {
            $N1 = $N2 = 0;

            $s1 = microtime(true);
            for ($i = 0; $i < 100; $i++) {
                $sql = str_replace("#i#", $i, $strSql);
            }
            $e1 = microtime(true);
            $N1 = $e1 - $s1;

            $s2 = microtime();
            for ($i = 0; $i < 100; $i++) {
                $this->_DbQuery(str_replace("#i#", $i, $strSql));
            }
            $e2 = microtime();
            $N2 = $e2 - $s2;

            if ($N2 > $N1) {
                $res[] = 100 / ($N2 - $N1);
            }
        }

        $this->_DbRemoveTemp();

        if (count($res)) {
            return array_sum($res) / doubleval(count($res));
        } else {
            return 0;
        }
    }

    /**
     * Mysql Insert test (with Joomla)
     */
    protected function _testMysqlInsertJoomla()
    {
        // prepare
        $this->_DbCreateTemp();
        $db = JFactory::getDbo();

        $st = microtime(true);
        for ($i = 0; $i < 500; $i++) {
            $db->setQuery('INSERT INTO `' . self::TEST_TABLE . '` values (' . $i . ')')->execute();
        };
        $fin = microtime(true);

        // drop
        $this->_DbRemoveTemp();

        return $fin - $st;
    }

    /**
     * Mysql select test (with Joomla)
     */
    protected function _testMysqlSelectJoomla()
    {
        // prepare
        $this->_DbCreateTemp();

        for ($i = 0; $i < 10000; $i++) {
            $this->_DbQuery('INSERT INTO `' . self::TEST_TABLE . '` VALUES (' . $i . ')');
        };

        $st = microtime(true);
        JFactory::getDbo()->setQuery('SELECT * FROM `' . self::TEST_TABLE . '` WHERE i > 0')->loadObjectList();
        $fin = microtime(true);

        // drop
        $this->_DbRemoveTemp();

        return $fin - $st;
    }

    /**
     * FileSystem complex test
     */
    protected function _testFsSimple()
    {
        $fileName = $this->app->jbpath->sysPath('tmp', "/jbzoo_test_#i#.php");

        $N1 = $N2 = 0;

        $s1 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            // noop
        }
        $e1 = microtime(true);
        $N1 = $e1 - $s1;

        $s2 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $fn = str_replace("#i#", $i, $fileName);
            $fh = fopen($fn, "wb");
            fclose($fh);
            unlink($fn);
        }
        $e2 = microtime(true);
        $N2 = $e2 - $s2;

        if ($N2 > $N1) {
            return 100 / ($N2 - $N1);
        }

        return 0;
    }

    /**
     * FileSystem complex test
     */
    protected function _testFsComplexJoomla()
    {
        $fileName = $this->app->jbpath->sysPath('tmp', "/jbzoo_test_joomla_#i#.php");

        $content =
            "<?php \$s='" . str_repeat("x", 1024) . "';?> \n" .
            "<?php /*" . str_repeat("y", 1024) . "*/?> \n" .
            "<?php \$r='" . str_repeat("z", 1024) . "';?>";

        $N1 = $N2 = 0;

        $s1 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            // noop
        }
        $e1 = microtime(true);
        $N1 = $e1 - $s1;

        $s2 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $fn = str_replace("#i#", $i, $fileName);
            JFile::write($fn, $content);
            include $fn;
            JFile::delete($fn);
        }
        $e2 = microtime(true);
        $N2 = $e2 - $s2;

        if ($N2 > $N1) {
            return 100 / ($N2 - $N1);
        }

        return 0;
    }

    /**
     * FileSystem write by one byte
     */
    protected function _testFsWrite()
    {
        $fileName = $this->app->jbpath->sysPath('tmp', "/jbzoo_test_write");

        if (!$handle = fopen($fileName, 'wb')) {
            die ('Can not open file for writing ' . $fileName);
        }

        $st = microtime(true);
        for ($i = 0; $i < 1000000; $i++) {
            fwrite($handle, '1');
        }
        $fin = microtime(true);

        fclose($handle);
        if (JFile::exists($fileName)) {
            unlink($fileName);
        }

        return $fin - $st;
    }

    /**
     * FileSystem read by one byte
     */
    protected function _testFsRead()
    {
        $fileName = $this->app->jbpath->sysPath('tmp', "/jbzoo_test_read");

        $handle = fopen($fileName, 'wb');
        for ($i = 0; $i < 1000000; $i++) {
            fwrite($handle, '1');
        }
        fclose($handle);

        $handle = fopen($fileName, 'r');

        $st = microtime(true);
        while (!feof($handle)) {
            fread($handle, 1);
        }
        $fin = microtime(true);

        fclose($handle);
        if (JFile::exists($fileName)) {
            unlink($fileName);
        }

        return $fin - $st;
    }

    /**
     * FileSystem complex test via Joomla API
     */
    protected function _testFsComplex()
    {
        $fileName = $this->app->jbpath->sysPath('tmp', "/jbzoo_test_#i#.php");

        $content =
            "<?php \$s='" . str_repeat("x", 1024) . "';?> \n" .
            "<?php /*" . str_repeat("y", 1024) . "*/?> \n" .
            "<?php \$r='" . str_repeat("z", 1024) . "';?>";

        $N1 = $N2 = 0;

        $s1 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            // noop
        }
        $e1 = microtime(true);
        $N1 = $e1 - $s1;

        $s2 = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $fn = str_replace("#i#", $i, $fileName);
            $fh = fopen($fn, "wb");
            fwrite($fh, $content);
            fclose($fh);
            include $fn;
            unlink($fn);
        }
        $e2 = microtime(true);
        $N2 = $e2 - $s2;

        if ($N2 > $N1) {
            return 100 / ($N2 - $N1);
        }

        return 0;
    }

    /**
     * Send email via mail()
     */
    protected function _testMailPhp()
    {
        mail($this->_testEmail, "JBZoo email test", "This is test message. Delete it.");
    }

    /**
     * Send email via Joomla API
     */
    protected function _testMailJoomla()
    {
        $mail = $this->app->mail->create();

        $mail->setSubject("JBZoo email test");
        $mail->setBody("This is test message. Delete it.");
        $mail->isHTML(false);
        $mail->addRecipient($this->_testEmail);

        $st = microtime(true);
        $mail->Send();
        $fin = microtime(true);

        return $fin - $st;
    }

    /**
     * Get session start time
     */
    protected function _testSessionInit()
    {
        $uri = new JUri(JUri::root());
        if (isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW']) {
            $uri->setPass($_SERVER['PHP_AUTH_PW']);
        }

        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']) {
            $uri->setUser($_SERVER['PHP_AUTH_USER']);
        }

        $requestUrl = $uri->toString() . 'media/zoo/applications/jbuniversal/tools/test-session.php';

        $values = array();
        for ($j = 0; $j < 10; $j++) {
            $values[] = (float)$this->app->jbhttp->request($requestUrl . '?nocache=' . mt_rand());
        }

        return array_sum($values) / doubleval(count($values));
    }

    /**
     * Engine start test
     */
    protected function _testEngineInit()
    {
        static $result;

        if (isset($_SERVER['REQUEST_TIME_FLOAT']) && !isset($result)) {
            $result = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        }

        return $result;
    }

    /**
     * Engine Memory
     * @return int
     */
    protected function _testMemoryPeak()
    {
        return memory_get_peak_usage(false);
    }

    /**
     * Get realpath cache size
     * @return mixed
     */
    protected function _testRealpathUsed()
    {
        return realpath_cache_size();
    }

    /**
     * Get realpath cache remaining
     * @return int
     */
    protected function _testRealpathRemaining()
    {
        $used = realpath_cache_size();
        $ini  = strtoupper(trim(ini_get('realpath_cache_size')));

        if (strpos($ini, 'K')) {
            $all = ((int)str_replace('K', '', $ini)) * 1024;
        } else if (strpos($ini, 'M')) {
            $all = ((int)str_replace('M', '', $ini)) * 1024 * 1024;
        }

        $result = $all - $used;
        if ($result < 0) {
            return 0;
        }

        return $result;
    }

    /**
     * Calc total score
     */
    protected function _testTotalScore()
    {
        return $this->_getTotalRate();
    }

    /**
     * Calc cpu score
     */
    protected function _testTotalCPU()
    {
        return $this->_getTotalRate('cpu');
    }

    /**
     * Calc mysql score
     */
    protected function _testTotalMySQL()
    {
        return $this->_getTotalRate('mysql');
    }

    /**
     * Calc mysql score
     */
    protected function _testTotalFS()
    {
        return $this->_getTotalRate('fs');
    }

    /**
     * Calc mysql score
     */
    protected function _testTotalEngine()
    {
        return $this->_getTotalRate('engine');
    }

    /**
     * Calc mysql score
     */
    protected function _testTotalMail()
    {
        return $this->_getTotalRate('mail');
    }

    /**
     * Get OS type
     */
    protected function _testVersionOS()
    {
        return PHP_OS;
    }

    /**
     * Get PHP version
     */
    protected function _testVersionPHP()
    {
        return phpversion();
    }

    /**
     * Get PHP version
     */
    protected function _testVersionMysql()
    {
        return JFactory::getDbo()->getVersion();
    }

    /**
     * Get Joomla version
     */
    protected function _testVersionJoomla()
    {
        return $this->app->jbversion->joomla();
    }

    /**
     * Get Joomla version
     */
    protected function _testVersionZoo()
    {
        return $this->app->jbversion->zoo();
    }

    /**
     * Get JBZoo version
     */
    protected function _testVersionJBZoo()
    {
        return $this->app->jbversion->jbzoo();
    }

    /**
     * Get system  loadavg
     */
    protected function _testSystemLoadavg()
    {
        if (function_exists('sys_getloadavg')) {
            $la = sys_getloadavg();
            return $la[1];
        }

        return '-';
    }

    /**
     *
     */
    protected function _testDBSend()
    {
        $dbUptime = $this->_testDBUptime(true);
        $db       = JFactory::getDbo();

        $res = $db->setQuery('SHOW GLOBAL STATUS LIKE "%Bytes_sent%"')->loadAssoc();

        return $res['Value'] / $dbUptime;
    }

    /**
     *
     */
    protected function _testDBUptime($inSeconds = false)
    {
        $db = JFactory::getDbo();
        $db->setQuery('SHOW GLOBAL STATUS LIKE "%Uptime%"');
        $res = $db->loadAssoc();

        if ($inSeconds) {
            return $res['Value'];
        }

        return $res['Value'] / 86400;
    }

    /**
     *
     */
    protected function _testDBConnections()
    {
        $dbUptime = $this->_testDBUptime(true);
        $db       = JFactory::getDbo();

        $db->setQuery('SHOW GLOBAL STATUS LIKE "%Connections%"');
        $res = $db->loadAssoc();

        return $res['Value'] / $dbUptime;
    }

    /**
     *
     */
    protected function _testDBSelectCount()
    {
        $dbUptime = $this->_testDBUptime(true);
        $db       = JFactory::getDbo();

        $db->setQuery('SHOW GLOBAL STATUS LIKE "%Com_select%"');
        $res = $db->loadAssoc();

        return $res['Value'] / $dbUptime;
    }

    /**
     * Calc total score
     */
    protected function _getTotalRate($group = null)
    {
        $values = $this->_jbsession->getGroup($this->_sessionGroup);

        $result = array();

        $stdValues = $this->getStdValues();
        foreach ($values as $testName => $value) {

            $std = $stdValues[$testName];
            list($testGroup) = explode('_', $testName);

            if (!in_array($testGroup, array('cpu', 'mysql', 'fs', 'engine', 'mail'))) {
                continue;
            }

            if (($group && $group != $testGroup)) {
                continue;
            }

            $rel = $value / $std['std'];

            if ($std['type'] == 'less' && $value > $std['std']) {
                $result[$testName] = -(100 - ((1 / $rel) * 100));

            } else if ($std['type'] == 'more' && $value < $std['std']) {
                $result[$testName] = -(100 - ($rel * 100));

            } else if ($std['type'] == 'more') {
                $result[$testName] = $rel * 100;

            } else if ($std['type'] == 'less') {
                $result[$testName] = (1 - $rel) * 100;

            } else {
                $result[$testName] = $rel * 100;
            }
        }

        $totalRes = (array_sum($result) / count($result));
        return $totalRes;
    }

    /**
     * Get fail test count
     */
    protected function _testTotalFail()
    {
        $values = $this->_jbsession->getGroup($this->_sessionGroup);
        $result = 0;

        $stdValues = $this->getStdValues();

        foreach ($values as $testName => $value) {

            $std = $stdValues[$testName];
            if ($std['type'] == 'less' && $value > $std['std']) {
                $result++;
            } else if ($std['type'] == 'more' && $value < $std['std']) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        static $result;

        if (!isset($result)) {

            $values = $this->_jbsession->getGroup($this->_sessionGroup);
            $values = $this->app->data->create($values);

            $result = array(
                'engine'  => array(
                    array(
                        'key'      => 'engine_init',
                        'type'     => 'less',
                        'value'    => $values->get('engine_init', '-'),
                        'standard' => 0.250,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'memory_peak',
                        'type'     => 'less',
                        'value'    => $values->get('memory_peak', '-'),
                        'standard' => 15728640,
                        'postfix'  => '',
                    ),
                ),
                'cpu'     => array(
                    array(
                        'key'      => 'cpu_complex',
                        'type'     => 'more',
                        'value'    => $values->get('cpu_complex', '-'),
                        'standard' => 9.0,
                        'postfix'  => 'mil_in_sec',
                    ),
                    array(
                        'key'      => 'cpu_sin',
                        'type'     => 'less',
                        'value'    => $values->get('cpu_sin', '-'),
                        'standard' => 0.300,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'cpu_concat_dot',
                        'type'     => 'less',
                        'value'    => $values->get('cpu_concat_dot', '-'),
                        'standard' => 0.200,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'cpu_concat_quotes',
                        'type'     => 'less',
                        'value'    => $values->get('cpu_concat_quotes', '-'),
                        'standard' => 0.250,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'cpu_concat_array',
                        'type'     => 'less',
                        'value'    => $values->get('cpu_concat_array', '-'),
                        'standard' => 0.600,
                        'postfix'  => 'in_sec',
                    ),
                ),
                'mysql'   => array(
                    array(
                        'key'      => 'mysql_connect',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_connect', '-'),
                        'standard' => 0.005,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_sin',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_sin', '-'),
                        'standard' => 0.100,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_insert',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_insert', '-'),
                        'standard' => 3.000,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_select',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_select', '-'),
                        'standard' => 0.030,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_insert_joomla',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_insert_joomla', '-'),
                        'standard' => 0.250,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_select_joomla',
                        'type'     => 'less',
                        'value'    => $values->get('mysql_select_joomla', '-'),
                        'standard' => 0.030,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mysql_select_advance',
                        'type'     => 'more',
                        'value'    => $values->get('mysql_select_advance', '-'),
                        'standard' => 7800,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'mysql_insert_advance',
                        'type'     => 'more',
                        'value'    => $values->get('mysql_insert_advance', '-'),
                        'standard' => 5600,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'mysql_replace_advance',
                        'type'     => 'more',
                        'value'    => $values->get('mysql_replace_advance', '-'),
                        'standard' => 5800,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'db_uptime',
                        'type'     => 'more',
                        'value'    => $values->get('db_uptime', '-'),
                        'standard' => 1,
                        'postfix'  => 'days',
                    ),
                    array(
                        'key'      => 'db_send',
                        'type'     => 'less',
                        'value'    => $values->get('db_send', '-'),
                        'standard' => 100000000,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'db_connections',
                        'type'     => 'less',
                        'value'    => $values->get('db_connections', '-'),
                        'standard' => 300,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'db_select_count',
                        'type'     => 'less',
                        'value'    => $values->get('db_select_count', '-'),
                        'standard' => 10000,
                        'postfix'  => '',
                    ),
                ),
                'fs'      => array(
                    array(
                        'key'      => 'fs_simple',
                        'type'     => 'more',
                        'value'    => $values->get('fs_simple', '-'),
                        'standard' => 20000,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'fs_complex',
                        'type'     => 'more',
                        'value'    => $values->get('fs_complex', '-'),
                        'standard' => 8000,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'fs_complex_joomla',
                        'type'     => 'more',
                        'value'    => $values->get('fs_complex_joomla', '-'),
                        'standard' => 6000,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'fs_write',
                        'type'     => 'less',
                        'value'    => $values->get('fs_write', '-'),
                        'standard' => 3.500,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'fs_read',
                        'type'     => 'less',
                        'value'    => $values->get('fs_read', '-'),
                        'standard' => 0.350,
                        'postfix'  => 'in_sec',
                    ),
                ),
                'mail'    => array(
                    array(
                        'key'      => 'mail_joomla',
                        'type'     => 'less',
                        'value'    => $values->get('mail_joomla', '-'),
                        'standard' => 0.120,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'mail_php',
                        'type'     => 'less',
                        'value'    => $values->get('mail_php', '-'),
                        'standard' => 0.100,
                        'postfix'  => 'in_sec',
                    ),
                ),
                'others'  => array(
                    array(
                        'key'      => 'session_init',
                        'type'     => 'less',
                        'value'    => $values->get('session_init', '-'),
                        'standard' => 0.005,
                        'postfix'  => 'in_sec',
                    ),
                    array(
                        'key'      => 'realpath_used',
                        'type'     => 'less',
                        'value'    => $values->get('realpath_used', '-'),
                        'standard' => 4 * 1024 * 1024,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'realpath_remaining',
                        'type'     => 'more',
                        'value'    => $values->get('realpath_remaining', '-'),
                        'standard' => 262144,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'system_loadavg',
                        'type'     => 'less',
                        'value'    => $values->get('system_loadavg', '-'),
                        'standard' => 5.0,
                        'postfix'  => '',
                    ),
                ),
                'version' => array(
                    array(
                        'key'      => 'version_os',
                        'type'     => 'none',
                        'value'    => $values->get('version_os', '-'),
                        'standard' => 'Linux-like system',
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'version_php',
                        'type'     => 'none',
                        'value'    => $values->get('version_php', '-'),
                        'standard' => '5.3.x+',
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'version_mysql',
                        'type'     => 'none',
                        'value'    => $values->get('version_mysql', '-'),
                        'standard' => '5.x+',
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'version_joomla',
                        'type'     => 'none',
                        'value'    => $values->get('version_joomla', '-'),
                        'standard' => '3.1.x+',
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'version_zoo',
                        'type'     => 'none',
                        'value'    => $values->get('version_zoo', '-'),
                        'standard' => '3.1.x+',
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'version_jbzoo',
                        'type'     => 'none',
                        'value'    => $values->get('version_jbzoo', '-'),
                        'standard' => '2.1.x+',
                        'postfix'  => '',
                    ),
                ),
                'result'  => array(
                    array(
                        'key'      => 'total_engine',
                        'type'     => 'more',
                        'value'    => $values->get('total_engine', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_cpu',
                        'type'     => 'more',
                        'value'    => $values->get('total_cpu', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_mysql',
                        'type'     => 'more',
                        'value'    => $values->get('total_mysql', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_mail',
                        'type'     => 'more',
                        'value'    => $values->get('total_mail', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_fs',
                        'type'     => 'more',
                        'value'    => $values->get('total_fs', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_score',
                        'type'     => 'more',
                        'value'    => $values->get('total_score', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                    array(
                        'key'      => 'total_fail',
                        'type'     => 'less',
                        'value'    => $values->get('total_fail', '-'),
                        'standard' => 0,
                        'postfix'  => '',
                    ),
                ),
            );
        }

        return $result;
    }

    /**
     * Value to human readble format
     * @param $value
     * @param $key
     * @return string
     */
    public function toFormat($value, $key)
    {
        if ($value == '-') {
            return $value;

        } else if ($key == 'total_fail') {
            return (int)$value;

        } else if ($key == 'system_loadavg') {
            return number_format($value, 2, '.', ' ');

        } else if (strpos($key, 'version') === 0) {
            return $value;

        } else if (strpos($key, 'total') === 0) {
            return number_format($value, 0, '.', ' ');

        } else if (in_array($key, array('memory_peak', 'realpath_remaining', 'realpath_used'))) {
            return $this->app->filesystem->formatFilesize($value);

        } else if ($value > 100) {
            return number_format($value, 0, '.', ' ');

        } else if ($value > 50) {
            return number_format($value, 1, '.', ' ');

        } else if ($value > 1) {
            return number_format($value, 2, '.', ' ');

        } else if ($value > 0.01) {
            return number_format($value, 2, '.', ' ');

        } else {
            return number_format($value, 3, '.', ' ');
        }
    }

    /**
     * Get standard values
     */
    public function getStdValues()
    {
        $testGroups = $this->getTestData();
        $result     = array();

        foreach ($testGroups as $tests) {
            foreach ($tests as $test) {
                $result[$test['key']] = array(
                    'std'  => $test['standard'],
                    'type' => $test['type'],
                );
            }

        }

        return $result;
    }

    /**
     * Check, is current value in test is warning
     * @param $value
     * @param $testName
     * @return bool
     */
    public function isAlert($value, $testName)
    {
        $stdValues = $this->getStdValues();
        $std       = $stdValues[$testName]['std'];

        $isAlert = false;
        if ($stdValues[$testName]['type'] == 'none') {
            $isAlert = -1;
        }

        if ($stdValues[$testName]['type'] == 'less' && $value > $std) {
            $isAlert = true;
        }

        if ($stdValues[$testName]['type'] == 'more' && $value < $std) {
            $isAlert = true;
        }

        return $isAlert;
    }

}
