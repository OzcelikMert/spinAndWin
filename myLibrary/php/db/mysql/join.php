<?php
namespace myLibrary\php\db\mysql;

class dbJoin{
    /**
     * Enter desired column values here <br>
     * Example: <b>inner("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function inner(array $join = array()) : string{
        return $this->join($join, Helper::INNER." ".Helper::JOIN);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>left("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function left(array $join = array()) : string{
        return $this->join($join, Helper::LEFT." ".Helper::JOIN);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>right("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function right(array $join = array()) : string{
        return $this->join($join, Helper::RIGHT." ".Helper::JOIN);
    }

    private function join(array $join, string $condition) : string {
        $values = "";

        if(count($join) > 0) {
            foreach ($join as $key => $value) {
                $values .= " {$condition} {$key} ON ";
                foreach ($value as $key2 => $value2) {
                    $values .= " {$key2} = {$value2} ".Helper::AND;
                }
                $values = substr($values, 0, -3);
            }
        }

        return $values;
    }
}