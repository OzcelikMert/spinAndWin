<?php
namespace myLibrary\php\db\mysql;

class dbWhere{
    /**
     * Enter desired column values here <br>
     * Example: <b>equals(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function equals(array $where = array()) : string{
        return $this->where($where, Helper::EQUALS);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>is_null(["column"])</b>
     * @param array $where
     * @return string
     */
    public function isNull(array $where = array()) : string{
        return $this->where($where, Helper::IS_NULL);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>like(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function like(array $where = array()) : string{
        return $this->where($where, Helper::LIKE);
    }

    /**
     * Enter unwanted column values here <br>
     * Example: <b>notLike(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function notLike(array $where = array()) : string{
        return $this->where($where, Helper::NOT_LIKE);
    }

    /**
     * Enter greater than column values here <br>
     * Example: <b>greater_than(["column" => value)</b>
     * Set the <b>$equals</b> to true if the value is greater than or equal
     * @param array $where
     * @param bool $equals
     * @return string
     */
    public function greaterThan(array $where = array(), bool $equals = false) : string{
        return $this->where($where, Helper::GREATER_THAN.(($equals) ? Helper::EQUALS : ""));
    }

    /**
     * Enter less than column values here <br>
     * Example: <b>less_than(["column", value)</b>
     * Set the <b>$equals</b> to true if the value is less than or equal
     * @param array $where
     * @param bool $equals
     * @return string
     */
    public function lessThan(array $where = array(), bool $equals = false) : string{
        return $this->where($where, Helper::LESS_THAN.(($equals) ? Helper::EQUALS : ""));
    }

    /**
     * Enter between column values here <br>
     * Example: <b>between(["column" => [value, value2]])</b>
     * @param array $where
     * @return string
     */
    public function between(array $where = array()) : string{
        $sql = "";
        foreach ($where as $key => $value){
            $sql = " $key ".Helper::BETWEEN." ".Helper::convertVarchar($value[0])." ".Helper::AND." ".Helper::convertVarchar($value[1])." ";
        }
        return $sql;
    }

    private function where(array $where, string $condition) : string {
        $values = "";

        if(count($where) > 0) {
            $values .= " ( ";
            foreach ($where as $key2 => $value2) {
                if (is_array($value2)) {
                    $values .= " ( ";
                    foreach ($value2 as $key3 => $value3) {
                        $key3 = (is_numeric($key3)) ? $key2 : $key3;
                        $values .= ($condition == Helper::IS_NULL)
                            ? " {$value3} {$condition} "
                            : " {$key3} {$condition} ".Helper::convertVarchar($value3)." ";
                        $values .= Helper:: OR;
                    }
                    $values = substr($values, 0, -2);
                    $values .= " ) ";
                } else {
                    $values .= ($condition == Helper::IS_NULL)
                        ? " {$value2} {$condition} "
                        : " {$key2} {$condition} ".Helper::convertVarchar($value2)." ";
                }
                $values .= Helper::AND;
            }
            $values = substr($values, 0, -3);
            $values .= " ) ";
        }

        return $values;
    }
}