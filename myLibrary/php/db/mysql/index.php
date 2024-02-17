<?php
namespace myLibrary\php\db\mysql;

use http\Exception;
use mysqli;

/**
 * Provides you convenience in database queries <b>(MYSQL)</b>.
 * @package db
 */
abstract class MySQL extends helper {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Mysql link given inside the <b>initialize</b> function
     * @var mysqli
     */
    protected mysqli $connect;

    /**
     * Used for insert command in mysql database.<br>
     * The single type <b>array $data</b> is like <i>array("column_name" => value, ...)</i>.<br>
     * The multiple type <b>array $data</b> is like <i>array(array("column_name" => value, ...), array(...))</i>.<br>
     * @param string $table_name
     * @param array $data
     * @return results
     */
    function dbInsert(
        string $table_name,
        array $data,
        bool $just_show_sql = false
    ) : results{
        $results = new results();
        try{
            $sql_insert = "";
            $sql_values = "";
            $is_multiple = false;

            foreach ($data as $single_key => $single_value) {
                if(is_array($single_value)){
                    if(!$is_multiple) $is_multiple = true;
                    $sql_values .= "(";
                    foreach ($single_value as $multiple_key => $multiple_value){
                        if((int)$single_key == 0) $sql_insert .= $multiple_key.",";
                        $sql_values .= self::convertVarchar($multiple_value).",";
                    }
                    $sql_values = substr($sql_values, 0, -1);
                    $sql_values .= "),";
                }else{
                    $sql_insert .= "$single_key,";
                    $sql_values .= self::convertVarchar($single_value).",";
                }
            }

            if(strlen($sql_insert) > 0 && strlen($sql_values) > 0){
                // Clear last character => sql_insert and sql_values
                $sql_insert = substr($sql_insert, 0, -1);
                $sql_values = ($is_multiple) ? substr($sql_values, 0, -1) : "(".substr($sql_values, 0, -1).")";

                $sql = "insert into {$table_name}({$sql_insert}) values {$sql_values};";
                $this->checkShowSQL($results->sql, $sql, $just_show_sql);
                if(!$just_show_sql) {
                    if ($query = mysqli_query($this->connect, $sql)) {
                        $results->status = true;
                        $results->insert_id = mysqli_insert_id($this->connect);
                    } else
                        $results->message = "MYSQL error description: " . mysqli_error($this->connect);
                }
            }
        }catch(Exception $exception){ $results->message = "Function error description:".$exception; }

        return $results;
    }

    /**
     * Used for select command in mysql database.<br>
     * @param string | array $columns
     * @param string $table_name
     * @param string $joins
     * @param string $where
     * @param string $group_by
     * @param string $order_by
     * @param string $limit
     * @param string $union_columns
     * @param string $union_table_name
     * @param string $union_joins
     * @param string $union_where
     * @param string $union_group_by
     * @param string $union_order_by
     * @param string $union_limit
     * @return results
     */
    function dbSelect(
        mixed $columns,
        string $table_name,
        string $joins = "",
        string $where = "",
        string $group_by = "",
        string $order_by = "",
        string $limit = "",
        string $union_columns = "",
        string $union_table_name = "",
        string $union_joins = "",
        string $union_where = "",
        string $union_group_by = "",
        string $union_order_by = "",
        string $union_limit = "",
        bool $just_show_sql = false
    ) : results{
        $results = new results();
        try{
            $columns_ = "";
            if(is_array($columns)){
                foreach($columns as $column){
                    $columns_ .= "$column,";
                }
                $columns_ = substr($columns_, 0, -1);
            }else{
                $columns_ = $columns;
            }
            $sql = "select {$columns_} from {$table_name} {$joins} {$this->checkCondition(self::WHERE, $where)} {$this->checkCondition(self::GROUP_BY, $group_by)} {$this->checkCondition(self::ORDER_BY, $order_by)} {$this->checkCondition(self::LIMIT, $limit)}".((strlen($union_columns) > 0) ? " union select {$union_columns} from {$union_table_name} {$union_joins} {$this->checkCondition(self::WHERE, $union_where)} {$this->checkCondition(self::GROUP_BY, $union_group_by)} {$this->checkCondition(self::ORDER_BY, $union_order_by)} {$this->checkCondition(self::LIMIT, $union_limit)}" : "");
            $this->checkShowSQL($results->sql, $sql, $just_show_sql);
            if(!$just_show_sql) {
                $query = mysqli_query($this->connect, $sql);
                if ($query) {
                    $results->status = true;
                    while ($rows = mysqli_fetch_assoc($query)) {
                        array_push($results->rows, $rows);
                    }
                } else {
                    $results->message = "MYSQL error description: " . mysqli_error($this->connect);
                }
            }
        }catch(Exception $exception){ $results->message = "Function error description: ".$exception; }

        return $results;
    }

    /**
     * Used for update command in mysql database.<br>
     * @param string $table_name
     * @param array $columns
     * @param string $joins
     * @param string $where
     * @param string $group_by
     * @param bool $checked_varchar
     * @return results
     */
    function dbUpdate(
        string $table_name,
        array $columns,
        string $joins = "",
        string $where = "",
        string $group_by = "",
        bool $checked_varchar = true,
        bool $just_show_sql = false
    ) : results{
        $results = new results();
        try{
            $columns_ = "";
            foreach($columns as $key => $value){
                $columns_ .= "{$key}=".(($checked_varchar) ? self::convertVarchar($value) : $value).",";
            }
            $columns_ = substr($columns_, 0, -1);
            $sql = "update {$table_name} {$joins} set {$columns_} {$this->checkCondition(self::WHERE, $where)} {$this->checkCondition(self::GROUP_BY, $group_by)}";
            $this->checkShowSQL($results->sql, $sql, $just_show_sql);
            if(!$just_show_sql) {
                if (mysqli_query($this->connect, $sql)) {
                    $results->status = true;
                } else {
                    $results->message = "MYSQL error description: " . mysqli_error($this->connect);
                }
            }
        }catch(Exception $exception){ $results->message = "Function error description:".$exception; }

        return $results;
    }

    /**
     * Used for delete command in mysql database.<br>
     * @param string $table_name
     * @param string $joins
     * @param string $where
     * @param string $order_by
     * @param string $limit
     * @return results
     */
    function dbDelete(
        string $table_name,
        string $joins = "",
        string $where  = "",
        string $order_by  = "",
        string $limit = "",
        bool $just_show_sql = false
    ) : results{
        $results = new results();
        try{
            $sql = "delete from {$table_name} {$joins} {$this->checkCondition(self::WHERE, $where)} {$this->checkCondition(self::ORDER_BY, $order_by)} {$this->checkCondition(self::LIMIT, $limit)}";
            $this->checkShowSQL($results->sql, $sql, $just_show_sql);
            if(!$just_show_sql) {
                if (mysqli_query($this->connect, $sql)) {
                    $results->status = true;
                } else {
                    $results->message = "MYSQL error description: " . mysqli_error($this->connect);
                }
            }
        }catch(Exception $exception){ $results->message = "Function error description:".$exception; }

        return $results;
    }

    /**
     * Database sets are made.<br>
     * Connection is made.
     */
    abstract protected function initialize() : void;
}