<?php

namespace Botble\Backup\Supports\MySql;

use Exception;
use PDO;

/**
 * TypeAdapter Factory.
 */
abstract class TypeAdapterFactory
{
    protected $dbHandler = null;
    protected $dumpSettings = [];

    /**
     * @param string $type Type of database factory to create (Mysql, Sqlite,...)
     * @param PDO $dbHandler
     *
     * @throws Exception
     */
    public static function create($type, $dbHandler = null, $dumpSettings = [])
    {
        $type = ucfirst(strtolower($type));
        if (! TypeAdapter::isValid($type)) {
            throw new Exception('Database type support for (' . $type . ') not yet available');
        }
        $method = __NAMESPACE__ . '\\' . 'TypeAdapter' . $type;

        return new $method($dbHandler, $dumpSettings);
    }

    public function __construct($dbHandler = null, $dumpSettings = [])
    {
        $this->dbHandler = $dbHandler;
        $this->dumpSettings = $dumpSettings;
    }

    /**
     * function databases Add sql to create and use database.
     *
     * @todo make it do something with sqlite
     */
    public function databases()
    {
        return '';
    }

    public function showCreateTable($tableName)
    {
        return "SELECT tbl_name as 'Table', sql as 'Create Table' " .
            'FROM sqlite_master ' .
            "WHERE type='table' AND tbl_name='" . $tableName . "'";
    }

    /**
     * function createTable Get table creation code from database.
     *
     * @todo make it do something with sqlite
     */
    public function createTable($row)
    {
        return '';
    }

    public function showCreateView($viewName)
    {
        return "SELECT tbl_name as 'View', sql as 'Create View' " .
            'FROM sqlite_master ' .
            "WHERE type='view' AND tbl_name='" . $viewName . "'";
    }

    /**
     * function createView Get view creation code from database.
     *
     * @todo make it do something with sqlite
     */
    public function createView($row)
    {
        return '';
    }

    /**
     * function showCreateTrigger Get trigger creation code from database.
     *
     * @todo make it do something with sqlite
     */
    public function showCreateTrigger($triggerName)
    {
        return '';
    }

    /**
     * function createTrigger Modify trigger code, add delimiters, etc.
     *
     * @todo make it do something with sqlite
     */
    public function createTrigger($triggerName)
    {
        return '';
    }

    /**
     * function createProcedure Modify procedure code, add delimiters, etc.
     *
     * @todo make it do something with sqlite
     */
    public function createProcedure($procedureName)
    {
        return '';
    }

    /**
     * function createFunction Modify function code, add delimiters, etc.
     *
     * @todo make it do something with sqlite
     */
    public function createFunction($functionName)
    {
        return '';
    }

    public function showTables()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='table'";
    }

    public function showViews()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='view'";
    }

    public function showTriggers()
    {
        return "SELECT name FROM sqlite_master WHERE type='trigger'";
    }

    public function showColumns()
    {
        if (1 != func_num_args()) {
            return '';
        }

        $args = func_get_args();

        return "pragma table_info(${args[0]})";
    }

    public function showProcedures()
    {
        return '';
    }

    public function showFunctions()
    {
        return '';
    }

    public function showEvents()
    {
        return '';
    }

    public function setupTransaction()
    {
        return '';
    }

    public function startTransaction()
    {
        return 'BEGIN EXCLUSIVE';
    }

    public function commitTransaction()
    {
        return 'COMMIT';
    }

    public function lockTable()
    {
        return '';
    }

    public function unlockTable()
    {
        return '';
    }

    public function startAddLockTable()
    {
        return PHP_EOL;
    }

    public function endAddLockTable()
    {
        return PHP_EOL;
    }

    public function startAddDisableKeys()
    {
        return PHP_EOL;
    }

    public function endAddDisableKeys()
    {
        return PHP_EOL;
    }

    public function addDropDatabase()
    {
        return PHP_EOL;
    }

    public function addDropTrigger()
    {
        return PHP_EOL;
    }

    public function dropTable()
    {
        return PHP_EOL;
    }

    public function dropView()
    {
        return PHP_EOL;
    }

    /**
     * Decode column metadata and fill info structure.
     * type, is_numeric and is_blob will always be available.
     *
     * @param array $colType Array returned from "SHOW COLUMNS FROM tableName"
     *
     * @return array
     */
    public function parseColumnType($colType)
    {
        return [];
    }

    public function backupParameters()
    {
        return PHP_EOL;
    }

    public function restoreParameters()
    {
        return PHP_EOL;
    }
}
