<?php
namespace myLibrary\php\db\mysql;

class dbCase{
    /**
     * Enter desired column values here <br>
     * Example: <b>equals(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function equals(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, Helper::EQUALS);
    }


    /**
     * Enter desired column values here <br>
     * Example: <b>like(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function like(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, Helper::LIKE);
    }

    /**
     * Enter unwanted column values here <br>
     * Example: <b>not_like(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function notLike(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, Helper::NOT_LIKE);
    }

    /**
     * Enter greater than column values here <br>
     * Example: <b>greater_than(["column" => value], "then value", "else value")</b>
     * Set the <b>$equals</b> to true if the value is greater than or equal
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @param bool $equals
     * @return string
     */
    public function greaterThan(array $when, mixed $then, mixed $else = null, bool $equals = false) : string{
        return $this->case($when, $then, $else, Helper::GREATER_THAN.(($equals) ? Helper::EQUALS : ""));
    }

    /**
     * Enter less than column values here <br>
     * Example: <b>less_than(["column" => value], "then value", "else value")</b>
     * Set the <b>$equals</b> to true if the value is less than or equal
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @param bool $equals
     * @return string
     */
    public function lessThan(array $when, mixed $then, mixed $else = null, bool $equals = false) : string{
        return $this->case($when, $then, $else, Helper::LESS_THAN.(($equals) ? Helper::EQUALS : ""));
    }

    private function case(array $when, mixed $then, mixed $else, string $condition) : string {
        $values = "";

        $values .= " (CASE WHEN ";
        foreach ($when as $key => $value) $values .= " {$key} {$condition} {$value}";
        $values .= " THEN {$then} ";
        $values .= " ELSE {$else} ";
        $values .= " END) ";

        return $values;
    }
}