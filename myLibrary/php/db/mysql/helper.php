<?php
namespace myLibrary\php\db\mysql;

abstract class Helper extends Tags{
    protected function __construct() {
        $this->where = new dbWhere();
        $this->case = new dbCase();
        $this->join = new dbJoin();
    }

    /**
     * Do it <b>true</b> if u want to see sql query string
     * @var bool
     */
    public bool $showSQL = true;
    /**
     * Helps in where conditions
     * @var dbWhere
     */
    public dbWhere $where;
    /**
     * Helps in case conditions
     * @var dbCase
     */
    public dbCase $case;
    /**
     * Helps in join conditions
     * @var dbJoin
     */
    public dbJoin $join;

    public function asName(string $name, string $new_name) : string {
        return "{$name} as {$new_name}";
    }

    public function count(string $column) : string{
        return "COUNT($column)";
    }

    public function length(string $column) : string{
        return "LENGTH($column)";
    }

    public function max(string $column) : string{
        return "MAX($column)";
    }

    public function min(string $column) : string{
        return "MIN($column)";
    }

    public function sum(string $column) : string{
        return "SUM($column)";
    }

    public function ifNull(string $column, mixed $value) : string{
        return "IFNULL($column, $value)";
    }

    public function groupBy(string | array $column) : string{
       $value = "";
        if(is_array($column)){
            foreach ($column as $item) $value .= "{$item},";
            $value = substr($value, 0, -1);
        }else{
            $value = $column;
        }
        return $value;
    }

    public function orderBy(mixed $column, string $sort_type = parent::ASC) : string{
        $value = "";
        if(is_array($column)){
            foreach ($column as $item) $value .= "{$item},";
            $value = substr($value, 0, -1);
        }else{
            $value = $column;
        }
        return "{$value} {$sort_type}";
    }

    public function orderByMulti(array $strings) : string{
        $value = "";
        foreach ($strings as $string){
            $value .= $string.",";
        }
        return substr($value, 0, -1);
    }

    public function limit(array $limit) : string{
        return $limit[0].",".$limit[1];
    }

    public function substring(string $value, int $start, int $length) : string{
        return "SUBSTRING({$value}, {$start}, {$length})";
    }

    public function concat(string ...$string) : string{
        $concat = "";
        foreach ($string as $data){
            $concat .= "{$data},";
        }
        $concat = substr($concat, 0, -1);
        return "CONCAT({$concat})";
    }

    /**
     * If the entered value is a string, it converts to varchar
     * @param mixed $value
     * @return mixed
     */
    public static function convertVarchar(mixed $value) : mixed{
        return (((is_string($value) && !strpos($value, "(CASE WHEN")) || empty($value)) && !is_numeric($value)) ? "'{$value}'" : $value;
    }

    protected function checkCondition(string $condition, string $condition_values) : string{
        return ($condition_values != null && strlen(trim($condition_values)) > 0) ? "$condition $condition_values" : "";
    }

    protected function checkShowSQL(string &$variable, string $sql, bool $just_show_sql = true) : string{
        if($this->show_sql || $just_show_sql){
            $variable = $sql;
        }

        return $variable;
    }
}